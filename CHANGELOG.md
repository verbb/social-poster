# Changelog

## 2.0.9 - 2020-01-10

### Added
- Allow posts to be deleted.

### Changed
- Now requires Craft 3.3+.

### Fixed
- Fix posts being sent on entry drafts and revisions.
- Fix duplicate submissions sent out when propagating entries on a multi-site.
- Add emoji support to posts.

## 2.0.8 - 2019-04-24

### Changed
- Remove `r_liteprofile` for Linked.in.

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
- Added support for Linked.in as a provider.
- Added support for Facebook Pages, along with Personal timelines.
- Added Title field to set the post title for Facebook and Linked.in posts.
- Added URL field to allow specific URLs to be used with Facebook and Linked.in posts.
- Added new setting for auto-post. Handy if you want the widget to appear, but not be default to on.

### Changed
- Each field (apart from enabled) can be selectively hidden on the entry widget, allowing only certain settings to be overridden per-entry.
- Better Guzzle exception handling for all providers.

### Fixed
- Prevent errors for non-supported providers.
- Fixed issue with checking plugin dependancies.

## 1.0.0 - 2016-06-30

- Initial release.
