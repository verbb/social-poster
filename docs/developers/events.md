# Events
Social Poster provides a collection of events for extending its functionality. Modules and plugins can register event listeners, typically in their `init()` methods, to modify Social Poster’s behavior.

## Post Events

### The `beforeSavePost` event
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

### The `afterSavePost` event
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

### The `beforeDeletePost` event
The event that is triggered before a post is deleted.

The `isValid` event property can be set to `false` to prevent the deletion from proceeding.

```php
use verbb\formie\elements\Form;
use yii\base\Event;

Event::on(Form::class, Form::EVENT_BEFORE_DELETE, function(Event $event) {
    $post = $event->sender;
    $event->isValid = false;
    // ...
});
```

### The `afterDeleteForm` event
The event that is triggered after a post is deleted.

```php
use verbb\formie\elements\Form;
use yii\base\Event;

Event::on(Form::class, Form::EVENT_AFTER_DELETE, function(Event $event) {
    $post = $event->sender;
    // ...
});
```

## Account Events

### The `beforeSaveAccount` event
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

### The `afterSaveAccount` event
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

### The `beforeDeleteAccount` event
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

### The `afterDeleteAccount` event
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
