<?php
$vendor = getenv('VERBB_SOCIAL_POSTER_TEST_VENDOR') ?: dirname(__DIR__, 2) . '/vendor';
$baseSource = getenv('VERBB_BASE_TEST_SOURCE') ?: dirname(__DIR__, 2) . '/vendor/verbb/base';

require $vendor . '/autoload.php';
require_once $vendor . '/yiisoft/yii2/Yii.php';
require_once $vendor . '/craftcms/cms/src/Craft.php';

spl_autoload_register(static function(string $class) use ($baseSource): void {
    $roots = [
        'verbb\\socialposter\\' => dirname(__DIR__, 2) . '/src/',
        'verbb\\auth\\' => (getenv('VERBB_AUTH_TEST_SOURCE') ?: dirname(__DIR__, 2) . '/vendor/verbb/auth') . '/src/',
        'verbb\\base\\' => $baseSource . '/src/',
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

use craft\base\ModelInterface;
use craft\helpers\Json;
use verbb\socialposter\SocialPoster;
use verbb\socialposter\accounts\Facebook;
use verbb\socialposter\accounts\Instagram;
use verbb\socialposter\base\AccountInterface;
use verbb\socialposter\controllers\AccountsController;
use verbb\socialposter\services\Accounts;
use yii\base\Model;
use yii\db\Connection;
use yii\web\Response;

putenv('FIXTURE_SECRET=fixture-secret-value');
$_SERVER['FIXTURE_SECRET'] = 'fixture-secret-value';

final class FixtureApp
{
    public bool $admin = false;
    public string $charset = 'UTF-8';
    public string $language = 'en-US';

    public function __construct(private Connection $db)
    {
    }

    public function getRequest(): object
    {
        return new class() {
            public function getIsConsoleRequest(): bool
            {
                return false;
            }
        };
    }

    public function getUser(): object
    {
        return new class($this->admin) {
            public function __construct(private bool $admin)
            {
            }

            public function getIsAdmin(): bool
            {
                return $this->admin;
            }
        };
    }

    public function getDb(): Connection
    {
        return $this->db;
    }

    public function getI18n(): object
    {
        return new class() {
            public function translate(string $category, string $message, array $params, ?string $language): string
            {
                return $message;
            }
        };
    }

    public function getPlugins(): object
    {
        return new class() {
            public function getPluginHandleByClass(string $class): ?string
            {
                return null;
            }
        };
    }
}

final class FixtureRequest
{
    public function __construct(private array $params)
    {
    }

    public function getParam(string $name, mixed $defaultValue = null): mixed
    {
        $value = $this->params;

        foreach (explode('.', $name) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $defaultValue;
            }

            $value = $value[$segment];
        }

        return $value;
    }
}

final class FixtureAccountsService extends Accounts
{
    public array $overrides = [];
    public ?AccountInterface $saved = null;

    public function getAccountById(int $id): ?AccountInterface
    {
        $row = Craft::$app->getDb()->createCommand('SELECT id, name, handle, enabled, autoPost, type, settings, sortOrder, dateCreated, dateUpdated FROM socialposter_accounts WHERE id = :id', [':id' => $id])->queryOne();

        return $row ? $this->createAccount($row) : null;
    }

    public function getAccountOverrides(string $handle): array
    {
        return $this->overrides;
    }

    public function saveAccount(AccountInterface $account, bool $runValidation = true): bool
    {
        $this->saved = $account;

        return true;
    }
}

final class FixtureAccountsController extends AccountsController
{
    public ?array $errors = null;

    public function requirePostRequest(): void
    {
    }

    public function asModelFailure(Model|ModelInterface $model, ?string $message = null, ?string $modelName = null, array $data = [], array $routeParams = []): ?Response
    {
        $this->errors = $model->getErrors();

        return null;
    }

    public function asModelSuccess(Model|ModelInterface $model, ?string $message = null, ?string $modelName = null, array $data = [], ?string $redirect = null): Response
    {
        return new Response();
    }
}

function check(string $label, bool $condition): void
{
    if (!$condition) {
        throw new RuntimeException("Failed: {$label}");
    }

    echo "{$label}: PASS\n";
}

$db = new Connection(['dsn' => 'sqlite::memory:']);
$db->createCommand('CREATE TABLE socialposter_accounts (id INTEGER PRIMARY KEY, name TEXT, handle TEXT, enabled INTEGER, autoPost INTEGER, type TEXT, settings TEXT, sortOrder INTEGER, cache TEXT, dateCreated TEXT, dateUpdated TEXT)')->execute();

$app = new FixtureApp($db);
Craft::$app = $app;

$accountsService = new FixtureAccountsService();
$plugin = (new ReflectionClass(SocialPoster::class))->newInstanceWithoutConstructor();
$plugin->set('accounts', $accountsService);
SocialPoster::$plugin = $plugin;

function storeAccount(int $id, string $type, array $settings): void
{
    Craft::$app->getDb()->createCommand()->delete('socialposter_accounts')->execute();
    Craft::$app->getDb()->createCommand()->insert('socialposter_accounts', [
        'id' => $id,
        'name' => 'Fixture',
        'handle' => 'fixture',
        'enabled' => 1,
        'autoPost' => 0,
        'type' => $type,
        'settings' => Json::encode($settings),
    ])->execute();
}

function saveAccount(array $params): FixtureAccountsController
{
    SocialPoster::$plugin->getAccounts()->saved = null;

    $controller = (new ReflectionClass(FixtureAccountsController::class))->newInstanceWithoutConstructor();
    $controller->request = new FixtureRequest($params + [
        'name' => 'Fixture',
        'handle' => 'fixture',
        'enabled' => '1',
    ]);
    $controller->actionSave();

    return $controller;
}

function refused(FixtureAccountsController $controller, string $attribute, string $message): bool
{
    return SocialPoster::$plugin->getAccounts()->saved === null && ($controller->errors[$attribute][0] ?? null) === $message;
}

function saved(FixtureAccountsController $controller): bool
{
    return SocialPoster::$plugin->getAccounts()->saved !== null && $controller->errors === null;
}

$referenceError = 'Only admins can use environment variables here.';
$credentialsError = 'Enter the credentials again after changing where this connects.';

// New accounts.
$controller = saveAccount(['type' => Facebook::class, 'types' => [Facebook::class => ['clientId' => '$FIXTURE_SECRET', 'clientSecret' => 'typed-secret']]]);
check('A non-admin cannot add a client ID that refers to an environment variable', refused($controller, 'clientId', $referenceError));

$controller = saveAccount(['type' => Facebook::class, 'types' => [Facebook::class => Json::encode(['clientId' => 'typed-id', 'clientSecret' => '${FIXTURE_SECRET}'])]]);
check('A non-admin cannot add a reference through JSON-encoded settings', refused($controller, 'clientSecret', $referenceError));

$controller = saveAccount(['type' => Facebook::class, 'types' => [Facebook::class => ['clientId' => 'typed-id', 'clientSecret' => 'typed-secret', 'message' => 'Now $5 off']]]);
check('A non-admin can save typed credentials', saved($controller));

$app->admin = true;
$controller = saveAccount(['type' => Facebook::class, 'types' => [Facebook::class => ['clientId' => '$FIXTURE_SECRET', 'clientSecret' => '$FIXTURE_SECRET']]]);
check('An admin can add references, which resolve in the client ID', saved($controller) && SocialPoster::$plugin->getAccounts()->saved->getClientId() === 'fixture-secret-value');
$app->admin = false;

// Saved accounts.
storeAccount(1, Facebook::class, ['clientId' => '$FIXTURE_SECRET', 'clientSecret' => '$FIXTURE_SECRET', 'pageId' => '11']);

$controller = saveAccount(['accountId' => '1', 'type' => Facebook::class, 'types' => [Facebook::class => ['clientId' => '$FIXTURE_SECRET', 'clientSecret' => '$FIXTURE_SECRET', 'pageId' => '12']]]);
check('A non-admin keeps references an admin saved', saved($controller) && SocialPoster::$plugin->getAccounts()->saved->clientId === '$FIXTURE_SECRET');

$controller = saveAccount(['accountId' => '1', 'type' => Facebook::class, 'types' => [Facebook::class => ['clientId' => '$CRAFT_DB_PASSWORD', 'clientSecret' => '$FIXTURE_SECRET']]]);
check('A non-admin cannot swap a saved reference for another', refused($controller, 'clientId', $referenceError));

$controller = saveAccount(['accountId' => '1', 'type' => Facebook::class, 'types' => [Facebook::class => ['clientId' => '$FIXTURE_SECRET', 'clientSecret' => '$FIXTURE_SECRET', 'pageId' => '$FIXTURE_SECRET']]]);
check('A saved reference can only stay in its own setting', refused($controller, 'pageId', $referenceError));

$controller = saveAccount(['accountId' => '1', 'type' => Instagram::class, 'types' => [Instagram::class => ['clientId' => '$FIXTURE_SECRET', 'clientSecret' => '$FIXTURE_SECRET']]]);
check('Changing provider cannot carry variable-backed credentials', refused($controller, 'clientId', $credentialsError));

$controller = saveAccount(['accountId' => '1', 'type' => Instagram::class, 'types' => [Instagram::class => ['clientId' => 'typed-id', 'clientSecret' => 'typed-secret']]]);
check('Changing provider with re-entered credentials is allowed', saved($controller));

$app->admin = true;
$controller = saveAccount(['accountId' => '1', 'type' => Instagram::class, 'types' => [Instagram::class => ['clientId' => '$CRAFT_DB_PASSWORD', 'clientSecret' => '$FIXTURE_SECRET']]]);
check('An admin can change provider and references', saved($controller));
$app->admin = false;

// Config file settings replace posted values, so they're compared against neither the form nor the database.
storeAccount(1, Facebook::class, ['clientId' => 'typed-id', 'clientSecret' => 'typed-secret', 'pageId' => '11']);
$accountsService->overrides = ['clientId' => '$FIXTURE_SECRET'];

$controller = saveAccount(['accountId' => '1', 'type' => Facebook::class, 'types' => [Facebook::class => ['clientId' => '$FIXTURE_SECRET', 'clientSecret' => 'typed-secret', 'pageId' => '12']]]);
check('Posting back a config file value is allowed', saved($controller));

$controller = saveAccount(['accountId' => '1', 'type' => Facebook::class, 'types' => [Facebook::class => ['clientId' => '$FIXTURE_SECRET', 'clientSecret' => '$FIXTURE_SECRET']]]);
check('Config file values do not allow other references', refused($controller, 'clientSecret', $referenceError));

storeAccount(1, Facebook::class, ['clientId' => 'typed-id', 'clientSecret' => '$FIXTURE_SECRET', 'pageId' => '11']);

$controller = saveAccount(['accountId' => '1', 'type' => Facebook::class, 'types' => [Facebook::class => ['clientId' => '$FIXTURE_SECRET', 'clientSecret' => '$FIXTURE_SECRET']]]);
check('Saved references beside config file values can stay', saved($controller));

$controller = saveAccount(['accountId' => '1', 'type' => Instagram::class, 'types' => [Instagram::class => ['clientId' => '$FIXTURE_SECRET', 'clientSecret' => '$FIXTURE_SECRET']]]);
check('Changing provider still needs saved credentials the config file does not set', refused($controller, 'clientSecret', $credentialsError));

$accountsService->overrides = [];

echo "Environment settings fixture passed.\n";
