# Changelog

## 4.0.8 - 2023-12-08

### Fixed
- Fix LinkedIn not switching from an image post to an article post, when containing both a link and image.
- Fix LinkedIn not respecting custom Guzzle config when fetching images when posts contain an image.

## 4.0.7 - 2023-10-25

### Fixed
- Implement `Element::trackChanges()` for Blitz compatibility.

## 4.0.6 - 2023-10-06
> {note} If you are using LinkedIn, your LinkedIn app will need to include new products. Refer to the [docs](https://verbb.io/craft-plugins/social-poster/docs/providers/linkedin).

### Changed
- Change LinkedIn from using deprecated “Shares API”.

## 4.0.5 - 2023-10-05

### Changed
- Change LinkedIn to use new OpenID Connect API.

## 4.0.4 - 2023-07-11

### Added
- Add ability to set `scopes` and `authorizationOptions` for accounts in config files.
- Add current site support to Redirect URI for multi-sites.

## 4.0.3 - 2023-06-25

### Fixed
- Fix Twitter missing `offline.access` scope, preventing refresh tokens.

## 4.0.2 - 2023-06-16

### Added
- Add `element` to `Account::EVENT_BEFORE_SEND_POST` and `Account::EVENT_AFTER_SEND_POST` events.
- Add the posted element to Payload models.

### Fixed
- Fix an issue where the Auth module wasn’t initialised properly for upgraded Social Poster installs.
- Fix emoji support for posts.
- Fix entry sidebar overwriting default Craft sidebar meta.

## 4.0.1 - 2023-05-27

### Added
- Add `Account::EVENT_BEFORE_SEND_POST` and `Account::EVENT_AFTER_SEND_POST` events.

### Fixed
- Fix Redirect URI not working correctly for multi-sites.

## 4.0.0 - 2023-02-01
> {note} This is a major release with big changes on how Accounts and Providers work. Please read the [migration](https://verbb.io/craft-plugins/social-poster/docs/get-started/migrating-from-v2) docs. You will be required to update your provider OAuth app settings and re-connect your accounts.

### Added
- Add `Payload` and `PostResponse` models for sending Posts to accounts for better consistency with how things are sent and received.
- Add [Auth module](https://github.com/verbb/auth) to handle all authentication.
- Add Instagram provider.

### Changed
- Revamped Accounts to combine with Providers. Provider OAuth settings are now managed in an Account.
- Overriding provider configs are now done via `accounts` in your config files.
- Updated provider icons.
- Updated plugin icon.

### Fixed
- Fix being able to access settings when `allowAdminChanges` with `false`.

### Removed
- Removed the concept of Providers. These are combined into Accounts.
- All token handling has been moved to the [Auth module](https://github.com/verbb/auth).
- Removed `SocialPoster::getTokens()` and  `SocialPoster::getProviders()` services.
- Removed `accounts/connect`,  `accounts/disconnect`,  `accounts/callback`

## 3.0.1 - 2022-10-12

### Fixed
- Fix lack of validation on account settings.
- Fix an error when saving new accounts.

## 3.0.0 - 2022-10-11

### Added
- Add `archiveTableIfExists()` to install migration.
- Memoize all services for performance.
- Add checks for registering events for performance.
- Add resave console command for elements.
- Add missing English translations.

### Changed
- Now requires PHP `8.0.2+`.
- Now requires Craft `4.0.0+`.
- Rename base plugin methods.

### Fixed
- Fix an error when uninstalling.

## 2.3.3 - 2022-03-29

### Fixed
- Fix some providers not having their scopes applied correctly.

## 2.3.2 - 2022-01-13

### Added
- Add support for saving Emoji's in default account message.

### Fixed
- Fix minor alignment issue for "Show in widget" when editing an account.

## 2.3.1 - 2021-11-14

### Added
- Add more logging for failed requests to APIs for all providers.

### Changed
- Update `guzzlehttp/oauth-subscriber:^0.6.0` dependancy to work with `guzzlehttp/psr7:^2.0`.

## 2.3.0 - 2021-11-06

### Changed
- Any `scope` options defined in your config files, will now overwrite default scopes, instead of merging. This is to better handle upcoming Facebook scope deprecations.

## 2.2.0 - 2021-03-12

### Changed
- Now requires Craft 3.6+.
- Updated OAuth dependancies to be compatible with Guzzle 7.

## 2.1.4 - 2020-04-28

### Fixed
- Prevent access to settings when `allowAdminChanges` is false

## 2.1.3 - 2020-04-16

### Fixed
- Fix logging error `Call to undefined method setFileLogging()`.

## 2.1.2 - 2020-04-15

### Changed
- File logging now checks if the overall Craft app uses file logging.
- Log files now only include `GET` and `POST` additional variables.

## 2.1.1 - 2020-04-13

### Added
- Add support LinkedIn for company pages. See [docs](https://verbb.io/craft-plugins/social-poster/docs/providers/linked-in).
- Updated Facebook and LinkedIn provider docs with more detailed instructions.

### Changed
- Remove `r_basicprofile` and `w_share` permissions for LinkedIn. These are no longer valid.
- Remove `visibility` settings for LinkedIn. No longer valid.

### Fixed
- Fix checking for valid asset field and asset.
- Provide some better error responses when connecting to providers.
- Fix front-end posting not working.

## 2.1.0 - 2020-01-29

### Added
- Craft 3.4 compatibility.

## 2.0.10 - 2020-01-10

### Fixed
- Fix posting to LinkedIn

## 2.0.9 - 2020-01-10

### Added
- Allow posts to be deleted.

### Changed
- Now requires Craft 3.2+.

### Fixed
- Fix posts being sent on entry drafts and revisions.
- Fix duplicate submissions sent out when propagating entries on a multi-site.
- Add emoji support to posts.

## 2.0.8 - 2019-04-24

### Changed
- Remove `r_liteprofile` for LinkedIn.

### Fixed
- Fix error when trying to post to Twitter.

## 2.0.7 - 2019-04-06

### Fixed
- Fix plugin name override not updating sidebar menu.
- Fix asset URL resolution for pictures.

## 2.0.6 - 2019-03-19

### Changed
- Remove toggle for posting when toggling enabled entry state.

### Fixed
- Fix incorrectly registering the wrong element.
- Fix past posts showing on new entries.

## 2.0.5 - 2019-03-19

### Fixed
- Fix error when trying to post to Facebook.

## 2.0.4 - 2019-03-18

### Fixed
- Fix Facebook authentication issues when `usePathInfo = false`, on some environments.

## 2.0.3 - 2019-03-07

### Fixed
- Fix `title` and `url` content not parsing twig.

## 2.0.2 - 2019-03-14

### Added
- Add override notice for settings fields.

### Fixed
- Fix deprecation error.
- Fix tabs not working in sidebar widget.
- Fix Facebook not sending the supplied image.
- Fix error when trying to use an image from an asset field.

## 2.0.1 - 2019-03-14

### Added
- Add `craft.socialPoster.posts`.

### Fixed
- Fix logging not working correctly in some areas.
- Fix provider settings not saving.
- Fix error when deleting account.

## 2.0.0 - 2019-03-13

### Added
- Craft 3.1+ release.
- Added Accounts.

## 1.2.2 - 2018-05-21

### Fixed
- Minor fix for sidebar icon.

## 1.2.1 - 2017-10-17

### Added
- Verbb marketing (new plugin icon, readme, etc).

## 1.2.0 - 2016-09-24

### Added
- Added Posts management sections in CP.
- Added ability to easily re-post past social media posts.
- Added `craft.socialPoster.posts()` template function to fetch successful posts on an entry.
- Added `url` attribute on posts - this returns the external URL to the corresponding post on the social media provider you've posted to - ie https://facebook.com/mypost.

### Changed
- Full JSON responses from providers are now saved for posterity, debugging, and future use.
- Improved Facebook posting, allowing you to properly post to either Profile, Page or Group.

## 1.1.2 - 2016-07-10

### Fixed
- Fixed version number (sorry!).

## 1.1.1 - 2016-07-09

### Added
- Added support for front-end submissions and overrides.

### Fixed
- Fixed some issues with checking for account options not yet set.
- Fixed issue with checking plugin dependancies.

## 1.1.0 - 2016-07-03

### Added
- Added support for LinkedIn as a provider.
- Added support for Facebook Pages, along with Personal timelines.
- Added Title field to set the post title for Facebook and LinkedIn posts.
- Added URL field to allow specific URLs to be used with Facebook and LinkedIn posts.
- Added new setting for auto-post. Handy if you want the widget to appear, but not be default to on.

### Changed
- Each field (apart from enabled) can be selectively hidden on the entry widget, allowing only certain settings to be overridden per-entry.
- Better Guzzle exception handling for all providers.

### Fixed
- Prevent errors for non-supported providers.
- Fixed issue with checking plugin dependancies.

## 1.0.0 - 2016-06-30

- Initial release.
