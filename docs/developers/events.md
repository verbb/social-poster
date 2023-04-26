# Events
Events can be used to extend the functionality of Social Poster.

## Post related events

### The `beforeSavePost` event
Plugins can get notified before a post is saved. Event handlers can prevent the post from getting sent by setting `$event->isValid` to false.

```php
use craft\events\ModelEvent;
use verbb\socialposter\elements\Post;
use yii\base\Event;

Event::on(Post::class, Post::EVENT_BEFORE_SAVE, function(ModelEvent $event) {
    $post = $event->sender;
    $event->isValid = false;
});
```

### The `afterSavePost` event
Plugins can get notified after a post has been saved

```php
use craft\events\ModelEvent;
use verbb\socialposter\elements\Post;
use yii\base\Event;

Event::on(Post::class, Post::EVENT_AFTER_SAVE, function(ModelEvent $event) {
    $post = $event->sender;
});
```

## Account related events
### The `beforeSaveAccount` event

Plugins can get notified before an account is being saved.

```php
use verbb\socialposter\events\AccountEvent;
use verbb\socialposter\services\Accounts;
use yii\base\Event;

Event::on(Accounts::class, Accounts::EVENT_BEFORE_SAVE_ACCOUNT, function(AccountEvent $event) {
    // Do something
});
```

### The `afterSaveAccount` event
Plugins can get notified after an account has been saved.

```php
use verbb\socialposter\events\AccountEvent;
use verbb\socialposter\services\Accounts;
use yii\base\Event;

Event::on(Accounts::class, Accounts::EVENT_AFTER_SAVE_ACCOUNT, function(AccountEvent $event) {
    // Do something
});
```

### The `beforeDeleteAccount` event
Plugins can get notified before an account is deleted

```php
use verbb\socialposter\events\AccountEvent;
use verbb\socialposter\services\Accounts;
use yii\base\Event;

Event::on(Accounts::class, Accounts::EVENT_BEFORE_DELETE_ACCOUNT, function(AccountEvent $event) {
    // Do something
});
```

### The `afterDeleteAccount` event
Plugins can get notified after an account has been deleted

```php
use verbb\socialposter\events\AccountEvent;
use verbb\socialposter\services\Accounts;
use yii\base\Event;

Event::on(Accounts::class, Accounts::EVENT_AFTER_DELETE_ACCOUNT, function(AccountEvent $event) {
    // Do something
});
```

## Token related events

### The `beforeSaveToken` event
Plugins can get notified before a token is being saved.

```php
use verbb\socialposter\events\TokenEvent;
use verbb\socialposter\services\Tokens;
use yii\base\Event;

Event::on(Tokens::class, Tokens::EVENT_BEFORE_SAVE_TOKEN, function(TokenEvent $event) {
    // Do something
});
```

### The `afterSaveToken` event
Plugins can get notified after a token has been saved.

```php
use verbb\socialposter\events\TokenEvent;
use verbb\socialposter\services\Tokens;
use yii\base\Event;

Event::on(Tokens::class, Tokens::EVENT_AFTER_SAVE_TOKEN, function(TokenEvent $event) {
    // Do something
});
```

### The `beforeDeleteToken` event
Plugins can get notified before an token is deleted

```php
use verbb\socialposter\events\TokenEvent;
use verbb\socialposter\services\Tokens;
use yii\base\Event;

Event::on(Tokens::class, Tokens::EVENT_BEFORE_DELETE_TOKEN, function(TokenEvent $event) {
    // Do something
});
```

### The `afterDeleteToken` event
Plugins can get notified after a token has been deleted

```php
use verbb\socialposter\events\TokenEvent;
use verbb\socialposter\services\Tokens;
use yii\base\Event;

Event::on(Tokens::class, Tokens::EVENT_AFTER_DELETE_TOKEN, function(TokenEvent $event) {
    // Do something
});
```

## Oauth related events

### The `afterOauthCallback` event

```php
use verbb\socialposter\controllers\AccountsController;
use verbb\socialposter\events\OauthTokenEvent;
use yii\base\Event;

Event::on(AccountsController::class, AccountsController::EVENT_AFTER_OAUTH_CALLBACK, function(OauthTokenEvent $event) {
    // Do something
});
```
