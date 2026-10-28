<?php
$vendor = getenv('VERBB_SOCIAL_POSTER_TEST_VENDOR') ?: dirname(__DIR__) . '/vendor';
require $vendor . '/autoload.php';
require_once $vendor . '/yiisoft/yii2/Yii.php';
require_once $vendor . '/craftcms/cms/src/Craft.php';

spl_autoload_register(static function(string $class): void {
    $roots = [
        'verbb\\socialposter\\' => dirname(__DIR__) . '/src/',
        'verbb\\auth\\' => (getenv('VERBB_AUTH_TEST_SOURCE') ?: dirname(__DIR__) . '/vendor/verbb/auth') . '/src/',
    ];

    foreach ($roots as $prefix => $root) {
        if (str_starts_with($class, $prefix)) {
            $path = $root . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';

            if (is_file($path)) {
                require $path;
            }
        }
    }
}, true, true);

use craft\elements\Entry;
use craft\models\Site;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use verbb\auth\models\Token;
use verbb\auth\providers\LinkedIn as LinkedInProvider;
use verbb\socialposter\accounts\LinkedIn;
use verbb\socialposter\models\Payload;
use verbb\socialposter\models\PostResponse;

function check(string $label, bool $condition): void
{
    if (!$condition) {
        throw new RuntimeException("Failed: {$label}");
    }

    echo "{$label}: PASS\n";
}

class FixtureProvider extends LinkedInProvider
{
    public function refreshToken(Token $token, bool $force = false): ?Token
    {
        return $token;
    }
}

class FixtureAccount extends LinkedIn
{
    public Token $fixtureToken;
    public Client $fixtureClient;

    public function getToken(): ?Token
    {
        return $this->fixtureToken;
    }

    public function getClient(): Client
    {
        return $this->fixtureClient;
    }

    public function setFixtureProvider(FixtureProvider $provider): void
    {
        $this->_oauthProvider = $provider;
    }

    protected function getPostExceptionResponse(mixed $exception): PostResponse
    {
        throw $exception;
    }
}

class FixtureEntry extends Entry
{
    public function init(): void
    {
    }

    public function getSite(): Site
    {
        return new Site(['baseUrl' => 'https://entry.example.test/nl/']);
    }
}

$binary = "\x89PNG\r\n\x1a\nfixture-image\0";
$cases = [
    [null, 'https://entry.example.test/blog/post', null, 'person'],
    [null, null, null, 'organization'],
    ['/uploads/photo.png', 'https://entry.example.test/blog/post', 'https://entry.example.test/uploads/photo.png', 'person'],
    ['uploads/photo.png', null, 'https://entry.example.test/nl/uploads/photo.png', 'organization'],
    ['https://cdn.example.test/photo.png?signature=123', null, 'https://cdn.example.test/photo.png?signature=123', 'person'],
    ['//cdn.example.test/photo.png', null, 'https://cdn.example.test/photo.png', 'organization'],
];

foreach ($cases as [$picture, $url, $imageUrl, $endpoint]) {
    $apiHistory = [];
    $downloadHistory = [];
    $responses = $endpoint === 'person' ? [new Response(200, ['Content-Type' => 'application/json'], '{"sub":"123"}')] : [];

    if ($picture) {
        $responses[] = new Response(200, ['Content-Type' => 'application/json'], '{"value":{"uploadUrl":"https://www.linkedin.com/dms-uploads/fixture","image":"urn:li:image:fixture"}}');
        $responses[] = new Response(201);
    }

    $responses[] = new Response(201, ['x-restli-id' => 'urn:li:share:fixture']);
    $apiMock = new MockHandler($responses);
    $apiStack = HandlerStack::create($apiMock);
    $apiStack->push(Middleware::history($apiHistory));
    $downloadMock = new MockHandler([new Response(200, ['Content-Type' => 'image/png'], $binary)]);
    $downloadStack = HandlerStack::create($downloadMock);
    $downloadStack->push(Middleware::history($downloadHistory));

    Craft::$app = new class($downloadStack) {
        public function __construct(private HandlerStack $handler)
        {
        }

        public function getVersion(): string
        {
            return '5.10.7';
        }

        public function getConfig(): object
        {
            return new class($this->handler) {
                public function __construct(private HandlerStack $handler)
                {
                }

                public function getConfigFromFile(string $name): array
                {
                    return ['handler' => $this->handler];
                }

                public function getGeneral(): object
                {
                    return (object)['httpProxy' => null, 'pathParam' => 'p'];
                }
            };
        }
    };

    $account = new FixtureAccount(['endpoint' => $endpoint, 'organizationId' => '456']);
    $account->fixtureToken = new Token(['tokenType' => Token::TOKEN_TYPE_OAUTH2, 'accessToken' => 'fixture-token']);
    $account->fixtureClient = new Client(['handler' => $apiStack]);
    $account->setFixtureProvider(new FixtureProvider(['restVersion' => '202609', 'restProtocolVersion' => '2.0.0']));
    $payload = new Payload(['element' => new FixtureEntry(), 'message' => 'Message', 'title' => 'Title', 'url' => $url, 'picture' => $picture]);
    $result = $account->sendPost($payload);
    $label = $picture ?? ($url ? 'article without image' : 'text only');
    check("{$label}: empty creation response is successful", $result->success === true && $result->data === []);
    check("{$label}: all API steps executed", $apiMock->count() === 0);
    $postRequest = $apiHistory[array_key_last($apiHistory)]['request'];
    $post = json_decode((string)$postRequest->getBody(), true, flags: JSON_THROW_ON_ERROR);
    check("{$label}: correct author", $post['author'] === ($endpoint === 'person' ? 'urn:li:person:123' : 'urn:li:organization:456'));
    check("{$label}: posts endpoint", (string)$postRequest->getUri() === 'https://api.linkedin.com/rest/posts' && $postRequest->getMethod() === 'POST');

    if ($picture) {
        $download = $downloadHistory[0]['request'];
        check("{$label}: image URL resolved", (string)$download->getUri() === $imageUrl);
        check("{$label}: download has no OAuth credentials", !$download->hasHeader('Authorization'));
        $upload = $apiHistory[count($apiHistory) - 2]['request'];
        check("{$label}: authenticated PUT uploads exact bytes", $upload->getMethod() === 'PUT' && (string)$upload->getBody() === $binary && $upload->getHeaderLine('Authorization') === 'Bearer fixture-token');
        $initialize = $apiHistory[count($apiHistory) - 3]['request'];
        $initialization = json_decode((string)$initialize->getBody(), true, flags: JSON_THROW_ON_ERROR);
        check("{$label}: initialization payload", $initialization === ['initializeUploadRequest' => ['owner' => $post['author']]]);
        check("{$label}: one content type", count($post['content']) === 1);
        check("{$label}: image attached", ($url ? $post['content']['article']['thumbnail'] : $post['content']['media']['id']) === 'urn:li:image:fixture');
    } else {
        check("{$label}: no image download", $downloadHistory === []);
    }
}
