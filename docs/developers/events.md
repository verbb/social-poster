# Events
Social Poster provides a collection of events for extending its functionality. Modules and plugins can register event listeners, typically in their `init()` methods, to modify Social Poster’s behavior.

## Post Events

### The `beforeSavePost` Event
The event that is triggered before a post is saved. You can set `$event->isValid` to false to prevent saving.

```php
use craft\events\ModelEvent;
use verbb\socialposter\elements\Post;
use yii\base\Event;

Event::on(Post::class, Post::EVENT_BEFORE_SAVE, function(ModelEvent $event) {
    $post = $event->sender;
    $event->isValid = false;
    // ...
});
```

### The `afterSavePost` Event
The event that is triggered after a post is saved.

```php
use craft\events\ModelEvent;
use verbb\socialposter\elements\Post;
use yii\base\Event;

Event::on(Post::class, Post::EVENT_AFTER_SAVE, function(ModelEvent $event) {
    $post = $event->sender;
    // ...
});
```

### The `beforeDeletePost` Event
The event that is triggered before a post is deleted.

The `isValid` event property can be set to `false` to prevent the deletion from proceeding.

```php
use verbb\socialposter\elements\Post;
use yii\base\Event;

Event::on(Post::class, Post::EVENT_BEFORE_DELETE, function(Event $event) {
    $post = $event->sender;
    $event->isValid = false;
    // ...
});
```

<span id="the-afterdeleteform-event"></span>

### The `afterDeletePost` Event
The event that is triggered after a post is deleted.

```php
use verbb\socialposter\elements\Post;
use yii\base\Event;

Event::on(Post::class, Post::EVENT_AFTER_DELETE, function(Event $event) {
    $post = $event->sender;
    // ...
});
```

### The `beforeSendPost` Event
The event that is triggered before an account sends a post.

The `isValid` event property can be set to `false` to prevent the post from being sent.

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

Set `$event->isValid` to `false` to make the send operation return a failure result after receiving the provider response. The remote request has already happened: this does not delete or undo a post on the provider. Check the remote result before retrying.

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

### The `beforeSaveAccount` Event
The event that is triggered before an account is saved.

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
