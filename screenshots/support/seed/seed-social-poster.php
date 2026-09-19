/** Seed genuine Social Poster accounts, an entry and a deterministic post state. */

use craft\elements\Entry;
use craft\fields\Assets;
use craft\fieldlayoutelements\CustomField;
use craft\fieldlayoutelements\entries\EntryTitleField;
use craft\helpers\Json;
use craft\models\EntryType;
use craft\models\FieldLayout;
use craft\models\FieldLayoutTab;
use craft\models\Section;
use craft\models\Section_SiteSettings;
use modules\socialposterscreenshots\ScreenshotFacebookAccount;
use modules\socialposterscreenshots\ScreenshotInstagramAccount;
use modules\socialposterscreenshots\ScreenshotLinkedInAccount;
use modules\socialposterscreenshots\ScreenshotTwitterAccount;
use verbb\socialposter\elements\Post;
use verbb\socialposter\SocialPoster;

$screenshotState = $screenshotState ?? 'composer';
$fields = Craft::$app->getFields();
$entries = Craft::$app->getEntries();
$elements = Craft::$app->getElements();
$site = Craft::$app->getSites()->getPrimarySite();
$fieldHandle = 'socialPosterImage';
$sectionHandle = 'socialPosterArticles';

$imageField = $fields->getFieldByHandle($fieldHandle);

if (!$imageField instanceof Assets) {
    $imageField = new Assets([
        'name' => 'Article image',
        'handle' => $fieldHandle,
        'sources' => '*',
        'viewMode' => 'large',
        'limit' => 1,
    ]);

    if (!$fields->saveField($imageField)) {
        throw new RuntimeException('Unable to save screenshot asset field: ' . Json::encode($imageField->getErrors()));
    }
}

$section = $entries->getSectionByHandle($sectionHandle);

if (!$section) {
    $entryType = new EntryType([
        'name' => 'Social article',
        'handle' => $sectionHandle . 'Type',
    ]);
    $layout = new FieldLayout(['type' => Entry::class]);
    $tab = new FieldLayoutTab(['name' => Craft::t('app', 'Content'), 'layout' => $layout]);
    $tab->setElements([new EntryTitleField(), new CustomField($imageField)]);
    $layout->setTabs([$tab]);
    $entryType->setFieldLayout($layout);

    if (!$entries->saveEntryType($entryType)) {
        throw new RuntimeException('Unable to save Social Poster entry type: ' . Json::encode($entryType->getErrors()));
    }

    $section = new Section([
        'name' => 'Social articles',
        'handle' => $sectionHandle,
        'type' => Section::TYPE_CHANNEL,
    ]);
    $section->setEntryTypes([$entryType]);
    $section->setSiteSettings([new Section_SiteSettings([
        'siteId' => $site->id,
        'enabledByDefault' => true,
        'hasUrls' => false,
    ])]);

    if (!$entries->saveSection($section)) {
        throw new RuntimeException('Unable to save Social Poster section: ' . Json::encode($section->getErrors()));
    }
}

$entryType = $entries->getEntryTypesBySectionId($section->id)[0] ?? null;

if (!$entryType) {
    throw new RuntimeException('Social Poster section has no entry type.');
}

$saveEntry = static function(string $title, string $slug) use ($section, $entryType, $site, $elements): Entry {
    $entry = Entry::find()
        ->sectionId($section->id)
        ->slug($slug)
        ->siteId($site->id)
        ->status(null)
        ->one() ?? new Entry([
            'sectionId' => $section->id,
            'typeId' => $entryType->id,
            'siteId' => $site->id,
            'slug' => $slug,
            'enabled' => true,
        ]);
    $entry->title = $title;
    $entry->enabled = true;

    if (!$elements->saveElement($entry)) {
        throw new RuntimeException('Unable to save Social Poster entry: ' . Json::encode($entry->getErrors()));
    }

    return $entry;
};

$entry = $saveEntry('A weekend by the coast', 'a-weekend-by-the-coast');

$accountService = SocialPoster::$plugin->getAccounts();
$accountDefinitions = [
    [
        'type' => ScreenshotFacebookAccount::class,
        'name' => 'Facebook',
        'handle' => 'facebook',
        'message' => 'A new post “{title}” has been published on our website — {url}',
        'showImageField' => true,
        'imageField' => $fieldHandle,
    ],
    [
        'type' => ScreenshotTwitterAccount::class,
        'name' => 'Twitter',
        'handle' => 'twitter',
        'message' => 'New on the journal: {title} — {url}',
        'showImageField' => false,
        'imageField' => null,
    ],
];

if ($screenshotState === 'index') {
    $accountDefinitions[] = [
        'type' => ScreenshotLinkedInAccount::class,
        'name' => 'LinkedIn',
        'handle' => 'linkedin',
        'message' => 'Read the latest from our journal: {title} — {url}',
        'showImageField' => true,
        'imageField' => $fieldHandle,
    ];
    $accountDefinitions[] = [
        'type' => ScreenshotInstagramAccount::class,
        'name' => 'Instagram',
        'handle' => 'instagram',
        'message' => 'A new story from the coast: {title}',
        'showImageField' => true,
        'imageField' => $fieldHandle,
    ];
}
$savedAccounts = [];

foreach ($accountDefinitions as $definition) {
    $account = $accountService->getAccountByHandle($definition['handle']);

    if (!$account instanceof $definition['type']) {
        if ($account) {
            $accountService->deleteAccount($account);
        }

        $account = $accountService->createAccount(['type' => $definition['type']]);
    }

    $account->name = $definition['name'];
    $account->handle = $definition['handle'];
    $account->enabled = true;
    $account->autoPost = true;
    $account->clientId = 'screenshot-client';
    $account->clientSecret = 'screenshot-secret';
    $account->showTitle = false;
    $account->showUrl = false;
    $account->showMessage = true;
    $account->message = $definition['message'];
    $account->showImageField = $definition['showImageField'];
    $account->imageField = $definition['imageField'];

    if ($account instanceof ScreenshotFacebookAccount || $account instanceof ScreenshotInstagramAccount) {
        $account->pageId = 'screenshot-page';
    }

    if (!$accountService->saveAccount($account, false)) {
        throw new RuntimeException('Unable to save Social Poster account ' . $definition['handle'] . '.');
    }

    $savedAccounts[$definition['handle']] = $account;
}

foreach (Post::find()->status(null)->ownerId($entry->id)->all() as $existingPost) {
    $elements->deleteElement($existingPost, true);
}

if ($screenshotState === 'index') {
    $postDefinitions = [
        ['title' => 'A weekend by the coast', 'slug' => 'a-weekend-by-the-coast', 'account' => 'facebook', 'success' => true, 'date' => '2026-08-20 09:42:00'],
        ['title' => 'Meet the makers', 'slug' => 'meet-the-makers', 'account' => 'linkedin', 'success' => true, 'date' => '2026-08-18 14:18:00'],
        ['title' => 'Five places worth the detour', 'slug' => 'five-places-worth-the-detour', 'account' => 'twitter', 'success' => true, 'date' => '2026-08-16 11:05:00'],
        ['title' => 'The spring collection', 'slug' => 'the-spring-collection', 'account' => 'instagram', 'success' => false, 'date' => '2026-08-14 16:30:00'],
    ];

    foreach ($postDefinitions as $definition) {
        $owner = $saveEntry($definition['title'], $definition['slug']);
        $account = $savedAccounts[$definition['account']];
        $post = new Post([
            'ownerId' => $owner->id,
            'ownerSiteId' => $owner->siteId,
            'ownerType' => Entry::class,
            'accountId' => $account->id,
            'settings' => ['message' => $definition['title']],
            'success' => $definition['success'],
            'response' => $definition['success']
                ? ['id' => $definition['account'] . '-post', 'reasonPhrase' => 'Success']
                : ['statusCode' => 400, 'reasonPhrase' => 'Image required'],
            'data' => [],
        ]);
        $post->dateCreated = new DateTime($definition['date']);

        if (!$elements->saveElement($post)) {
            throw new RuntimeException('Unable to save Social Poster index post: ' . Json::encode($post->getErrors()));
        }
    }
} else if ($screenshotState !== 'composer') {
    $facebook = $savedAccounts['facebook'];
    $post = new Post([
        'ownerId' => $entry->id,
        'ownerSiteId' => $entry->siteId,
        'ownerType' => Entry::class,
        'accountId' => $facebook->id,
        'settings' => ['message' => 'A weekend by the coast'],
        'success' => $screenshotState === 'success',
        'response' => $screenshotState === 'success'
            ? ['id' => 'facebook-post-1042']
            : ['statusCode' => 400, 'reasonPhrase' => 'Bad Request'],
        'data' => [],
    ]);
    $post->dateCreated = new DateTime('2026-08-20 10:24:00');

    if (!$elements->saveElement($post)) {
        throw new RuntimeException('Unable to save Social Poster post: ' . Json::encode($post->getErrors()));
    }
}

echo Json::encode([
    'entryEditRoute' => parse_url((string)$entry->getCpEditUrl(), PHP_URL_PATH),
    'postsIndexRoute' => '/admin/social-poster/posts',
], JSON_THROW_ON_ERROR);
