<?php
namespace yii\base {
    class ActionEvent
    {
        public mixed $action = null;
        public mixed $sender = null;

        public function __construct(array $config = [])
        {
            $this->action = $config['action'] ?? null;
            $this->sender = $config['sender'] ?? null;
        }
    }

    class Event
    {
        public static array $handlers = [];

        public static function on(string $class, string $name, callable $handler): void
        {
            self::$handlers[$class][$name][] = $handler;
        }
    }
}

namespace yii\web {
    class BadRequestHttpException extends \RuntimeException
    {
    }

    class NotFoundHttpException extends \RuntimeException
    {
    }

    class Response
    {
        public mixed $data = null;
    }
}

namespace craft\base {
    class Element
    {
    }

    class Model
    {
    }

    class Plugin
    {
        public const EVENT_BEFORE_ACTION = 'beforeAction';

        public function init(): void
        {
        }
    }
}

namespace craft\web {
    class Controller
    {
        public bool $parentAllowsAction = true;
        public array $guardCalls = [];
        public mixed $request = null;

        public function beforeAction($action): bool
        {
            $this->guardCalls[] = 'parent';

            return $this->parentAllowsAction;
        }

        public function requireAcceptsJson(): void
        {
            $this->guardCalls[] = 'json';
        }

        public function requireCpRequest(): void
        {
            $this->guardCalls[] = 'cp';
        }

        public function requirePermission(string $permission): void
        {
            $this->guardCalls[] = 'permission:' . $permission;
        }

        public function requirePostRequest(): void
        {
            $this->guardCalls[] = 'post';
        }

        public function asJson(mixed $data): \yii\web\Response
        {
            $response = new \yii\web\Response();
            $response->data = $data;

            return $response;
        }
    }

    class UrlManager
    {
    }
}

namespace craft\controllers {
    class AppController extends \craft\web\Controller
    {
        public const EVENT_BEFORE_ACTION = 'beforeAction';
    }

    class ElementIndexesController extends \craft\web\Controller
    {
        public const EVENT_BEFORE_ACTION = 'beforeAction';
    }

    class ElementSearchController extends \craft\web\Controller
    {
        public const EVENT_BEFORE_ACTION = 'beforeAction';
    }

    class ElementSelectorModalsController extends \craft\web\Controller
    {
        public const EVENT_BEFORE_ACTION = 'beforeAction';
    }

    class RelationalFieldsController extends \craft\web\Controller
    {
        public const EVENT_BEFORE_ACTION = 'beforeAction';
    }
}

namespace craft\elements {
    class Entry
    {
    }

    class User
    {
        public function __construct(private array $permissions = [])
        {
        }

        public function can(string $permission): bool
        {
            return in_array($permission, $this->permissions, true);
        }
    }
}

namespace craft\helpers {
    class Html
    {
        public static function encode(?string $content, bool $doubleEncode = true): string
        {
            return htmlspecialchars((string)$content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
        }
    }
}

namespace verbb\socialposter\base {
    interface AccountInterface
    {
    }

    trait PluginTrait
    {
        public static mixed $plugin = null;
    }
}

namespace {
    require __DIR__ . '/../../src/controllers/AccountsController.php';
    require __DIR__ . '/../../src/controllers/PostsController.php';
    require __DIR__ . '/../../src/elements/Post.php';
    require __DIR__ . '/../../src/SocialPoster.php';

    use craft\controllers\AppController;
    use craft\controllers\ElementIndexesController;
    use craft\controllers\ElementSearchController;
    use craft\controllers\ElementSelectorModalsController;
    use craft\controllers\RelationalFieldsController;
    use craft\elements\User;
    use verbb\socialposter\SocialPoster;
    use verbb\socialposter\base\AccountInterface;
    use verbb\socialposter\controllers\AccountsController;
    use verbb\socialposter\controllers\PostsController;
    use verbb\socialposter\elements\Post;
    use yii\base\ActionEvent;
    use yii\base\Event;

    class FixtureAction
    {
        public function __construct(public string $id)
        {
        }
    }

    class FixtureRequest
    {
        public function __construct(private array $params = [])
        {
        }

        public function getParam(string $name): mixed
        {
            return $this->params[$name] ?? null;
        }

        public function getRequiredBodyParam(string $name): mixed
        {
            if (!array_key_exists($name, $this->params)) {
                throw new RuntimeException("Missing fixture body param: {$name}");
            }

            return $this->params[$name];
        }

        public function getBodyParam(string $name, mixed $defaultValue = null): mixed
        {
            return $this->params[$name] ?? $defaultValue;
        }
    }

    class FixtureAccount implements AccountInterface
    {
        public string $icon = '<svg class="fixture-icon"></svg>';
        public string $name;
        public string $primaryColor = '#123456';

        public function __construct(string $name = 'Fixture')
        {
            $this->name = $name;
        }

        public function getAccountSettings(string $setting, bool $useCache): array
        {
            return [
                'setting' => $setting,
                'useCache' => $useCache,
            ];
        }
    }

    class FixtureAccountsService
    {
        public int $lookups = 0;

        public function getAccountByHandle(string $handle): ?FixtureAccount
        {
            $this->lookups++;

            return $handle === 'fixture' ? new FixtureAccount() : null;
        }
    }

    class FixturePlugin
    {
        public function __construct(private FixtureAccountsService $accounts)
        {
        }

        public function getAccounts(): FixtureAccountsService
        {
            return $this->accounts;
        }
    }

    class FixturePost extends Post
    {
        public ?AccountInterface $fixtureAccount = null;

        public function getAccount(): ?AccountInterface
        {
            return $this->fixtureAccount;
        }

        public function renderAttribute(string $attribute): string
        {
            return $this->attributeHtml($attribute);
        }
    }

    function check(string $label, bool $condition): void
    {
        if (!$condition) {
            throw new RuntimeException("Failed: {$label}");
        }

        echo "{$label}: PASS\n";
    }

    $accountsController = new AccountsController();
    check('Account actions require the CP and account-management permission',
        $accountsController->beforeAction(new FixtureAction('index')) &&
        $accountsController->guardCalls === ['parent', 'cp', 'permission:socialPoster-accounts']
    );

    $accountsController = new AccountsController();
    $accountsController->parentAllowsAction = false;
    check('Account guards do not bypass a parent denial',
        !$accountsController->beforeAction(new FixtureAction('index')) &&
        $accountsController->guardCalls === ['parent']
    );

    $postsController = new PostsController();
    check('Post actions require the CP and post-management permission',
        $postsController->beforeAction(new FixtureAction('index')) &&
        $postsController->guardCalls === ['parent', 'cp', 'permission:socialPoster-posts']
    );

    $accountsService = new FixtureAccountsService();
    SocialPoster::$plugin = new FixturePlugin($accountsService);
    $accountsController = new AccountsController();
    $accountsController->request = new FixtureRequest([
        'account' => 'fixture',
        'setting' => 'pages',
    ]);
    $response = $accountsController->actionRefreshSettings();
    check('Account settings refresh requires POST before provider lookup',
        $accountsController->guardCalls === ['post', 'json'] &&
        $accountsService->lookups === 1 &&
        $response->data === ['setting' => 'pages', 'useCache' => false]
    );

    $post = new Post();
    $permittedUser = new User(['socialPoster-posts']);
    $unpermittedUser = new User();

    foreach (['canView', 'canSave', 'canDuplicate', 'canDelete', 'canCreateDrafts'] as $method) {
        check("Post {$method} allows the registered post-management role", $post->$method($permittedUser));
        check("Post {$method} rejects users without the post-management role", !$post->$method($unpermittedUser));
    }

    $maliciousName = 'A &amp; B <img src=x onerror="alert(1)">';
    $fixturePost = new FixturePost();
    $fixturePost->fixtureAccount = new FixtureAccount($maliciousName);
    $accountHtml = $fixturePost->renderAttribute('account');

    check('Post account labels encode persisted account names',
        !str_contains($accountHtml, '<img') &&
        str_contains($accountHtml, 'A &amp;amp; B &lt;img src=x onerror=&quot;alert(1)&quot;&gt;')
    );
    check('Post account labels retain trusted provider markup',
        str_contains($accountHtml, '<svg class="fixture-icon"></svg>') &&
        str_contains($accountHtml, 'style="--bg-color: #123456"')
    );

    $plugin = new SocialPoster();
    $registerElementPermissions = new ReflectionMethod($plugin, '_registerElementPermissions');
    $registerElementPermissions->invoke($plugin);

    $genericElementControllers = [
        ElementIndexesController::class,
        ElementSearchController::class,
        ElementSelectorModalsController::class,
        RelationalFieldsController::class,
    ];

    foreach ($genericElementControllers as $controllerClass) {
        $handler = Event::$handlers[$controllerClass][ElementIndexesController::EVENT_BEFORE_ACTION][0] ?? null;
        check("The {$controllerClass} guard is registered", is_callable($handler));

        foreach ([Post::class, FixturePost::class] as $elementType) {
            $controller = new $controllerClass();
            $controller->request = new FixtureRequest(['elementType' => $elementType]);
            $handler(new ActionEvent([
                'action' => new FixtureAction('fixture'),
                'sender' => $controller,
            ]));
            check("{$controllerClass} protects {$elementType}",
                $controller->guardCalls === ['cp', 'permission:socialPoster-posts']
            );
        }

        $controller = new $controllerClass();
        $controller->request = new FixtureRequest(['elementType' => stdClass::class]);
        $handler(new ActionEvent([
            'action' => new FixtureAction('fixture'),
            'sender' => $controller,
        ]));
        check("{$controllerClass} leaves unrelated element types unchanged", $controller->guardCalls === []);
    }

    $appHandler = Event::$handlers[AppController::class][AppController::EVENT_BEFORE_ACTION][0] ?? null;
    check('The element-rendering guard is registered', is_callable($appHandler));

    $appController = new AppController();
    $appController->request = new FixtureRequest([
        'elements' => [
            ['type' => stdClass::class],
            ['type' => Post::class],
        ],
    ]);
    $appHandler(new ActionEvent([
        'action' => new FixtureAction('render-elements'),
        'sender' => $appController,
    ]));
    check('Generic Post chip/card rendering requires the CP and post-management permission',
        $appController->guardCalls === ['cp', 'permission:socialPoster-posts']
    );

    $appController = new AppController();
    $appController->request = new FixtureRequest([
        'elements' => [['type' => Post::class]],
    ]);
    $appHandler(new ActionEvent([
        'action' => new FixtureAction('render-components'),
        'sender' => $appController,
    ]));
    check('Unrelated App controller actions are unchanged', $appController->guardCalls === []);
}
