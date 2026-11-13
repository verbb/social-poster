# Events

Social Poster provides events for extending its functionality. Modules and plugins can register event listeners, typically in their `init()` methods, to change Social Poster’s behaviour.

## Post Events

Posts are Craft elements, so they trigger Craft’s own element events. Social Poster creates a post element each time it sends an entry to an account.

### The `beforeSave` Event
The event that is triggered before a post is saved.

Set `$event->isValid` to `false` to stop the post from being saved.

```php
use craft\events\ModelEvent;
use verbb\socialposter\elements\Post;
use yii\base\Event;

Event::on(Post::class, Post::EVENT_BEFORE_SAVE, function(ModelEvent $event) {
    $post = $event->sender;
    // ...
});
```

### The `afterSave` Event
The event that is triggered after a post is saved.

```php
use craft\events\ModelEvent;
use verbb\socialposter\elements\Post;
use yii\base\Event;

Event::on(Post::class, Post::EVENT_AFTER_SAVE, function(ModelEvent $event) {
    $post = $event->sender;
    $isNew = $event->isNew;
    // ...
});
```

### The `beforeDelete` Event
The event that is triggered before a post is deleted.

Set `$event->isValid` to `false` to stop the post from being deleted.

```php
use craft\events\ModelEvent;
use verbb\socialposter\elements\Post;
use yii\base\Event;

Event::on(Post::class, Post::EVENT_BEFORE_DELETE, function(ModelEvent $event) {
    $post = $event->sender;
    // ...
});
```

### The `afterDelete` Event
The event that is triggered after a post is deleted.

```php
use verbb\socialposter\elements\Post;
use yii\base\Event;

Event::on(Post::class, Post::EVENT_AFTER_DELETE, function(Event $event) {
    $post = $event->sender;
    // ...
});
```

## Send Events

### The `beforeSendPost` Event
The event that is triggered before an account sends a post.

You can change `$event->payload`, `$event->endpoint` and `$event->method` to adjust the request before it’s sent. Set `$event->isValid` to `false` to stop the post from being sent. Listen on `verbb\socialposter\base\Account` instead of `Twitter` to handle every account type.

```php
use verbb\socialposter\accounts\Twitter;
use verbb\socialposter\events\SendPostEvent;
use yii\base\Event;

Event::on(Twitter::class, Twitter::EVENT_BEFORE_SEND_POST, function(SendPostEvent $event) {
    $element = $event->element;
    $payload = $event->payload;
    $account = $event->account;
    $endpoint = $event->endpoint;
    $method = $event->method;
    // ...
});
```

### The `afterSendPost` Event
The event that is triggered after an account sends a post.

Set `$event->isValid` to `false` to make the send operation return a failure result after receiving the provider response. The remote request has already happened, so this doesn’t delete or undo a post on the provider. Check the remote result before retrying.

```php
use verbb\socialposter\accounts\Twitter;
use verbb\socialposter\events\SendPostEvent;
use yii\base\Event;

Event::on(Twitter::class, Twitter::EVENT_AFTER_SEND_POST, function(SendPostEvent $event) {
    $element = $event->element;
    $payload = $event->payload;
    $account = $event->account;
    $response = $event->response;
    // ...
});
```

## Account Events

### The `registerAccountTypes` Event
The event that is triggered when Social Poster builds the list of available account providers. Append your class to `$event->types` to register it. [Custom Providers](custom-providers.md) shows a complete provider class.

```php
use craft\events\RegisterComponentTypesEvent;
use modules\sitemodule\Example;
use verbb\socialposter\services\Accounts;
use yii\base\Event;

Event::on(Accounts::class, Accounts::EVENT_REGISTER_ACCOUNT_TYPES, function(RegisterComponentTypesEvent $event) {
    $event->types[] = Example::class;
});
```

### The `beforeSaveAccount` Event
The event that is triggered before an account is saved.

You can change `$event->account` before it’s validated and saved. This event can’t cancel the save.

```php
use verbb\socialposter\events\AccountEvent;
use verbb\socialposter\services\Accounts;
use yii\base\Event;

Event::on(Accounts::class, Accounts::EVENT_BEFORE_SAVE_ACCOUNT, function(AccountEvent $event) {
    $account = $event->account;
    $isNew = $event->isNew;
    // ...
});
```

### The `afterSaveAccount` Event
The event that is triggered after an account is saved.

```php
use verbb\socialposter\events\AccountEvent;
use verbb\socialposter\services\Accounts;
use yii\base\Event;

Event::on(Accounts::class, Accounts::EVENT_AFTER_SAVE_ACCOUNT, function(AccountEvent $event) {
    $account = $event->account;
    $isNew = $event->isNew;
    // ...
});
```

### The `beforeDeleteAccount` Event
The event that is triggered before an account is deleted.

This event can’t cancel the deletion.

```php
use verbb\socialposter\events\AccountEvent;
use verbb\socialposter\services\Accounts;
use yii\base\Event;

Event::on(Accounts::class, Accounts::EVENT_BEFORE_DELETE_ACCOUNT, function(AccountEvent $event) {
    $account = $event->account;
    // ...
});
```

### The `afterDeleteAccount` Event
The event that is triggered after an account is deleted.

```php
use verbb\socialposter\events\AccountEvent;
use verbb\socialposter\services\Accounts;
use yii\base\Event;

Event::on(Accounts::class, Accounts::EVENT_AFTER_DELETE_ACCOUNT, function(AccountEvent $event) {
    $account = $event->account;
    // ...
});
```
