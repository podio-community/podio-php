[7.0.3](#v7.0.2) / [UNRELEASED]
==================
* Bugfix: handle http errors correctly when using `return_raw_as_resource_only` to stream responses.

[7.0.2](#v7.0.2) / [2023-11-21]
==================
* Bugfix: Correct handling of `null` values for date/datetime fields. #244

[7.0.1](#v7.0.1) / [2023-11-07]
==================
* Bugfix: Require guzzlehttp/psr7 >= 1.7.0 for Util class by @toby-griffiths in #243.

[7.0.0](#v7.0.0) / [2023-08-22]
==================
* BREAKING: Replace static `Podio` client with instantiable `PodioClient` class. #228
* BREAKING: Replace `save` (and `completed`/`incompleted`/`destroy` on `PodioTask`) methods on instances with static methods #234
* BREAKING: Remove obsolete `PodioClient::secret` and `PodioClient::headers` properties.
* BREAKING: `Podio::debug` changed from public to protected: use `PodioClient::set_debug(..)`
* BREAKING: Kint is now an optional dependency of the package. Use `composer require kint-php/kint` to install it, if you need it.
* Bugfix: Error on fetching single contact with `PodioContact::get`.
* Bugfix: Setting values to empty array of several `PodioItemField` subtypes was broken.
* Bugfix: Debug output via Kint is now working again. #240
* See [migration guide](https://github.com/podio-community/podio-php/blob/master/MIGRATION_GUIDE_v7.md) for details.

[6.1.1](#v6.1.1) / 2023-06-12
==================
* Bugfix: PodioError.php fix for null requests by @mgithens in https://github.com/podio-community/podio-php/pull/227
* Bugfix: Added missing var to PodioError.php by @mgithens in https://github.com/podio-community/podio-php/pull/226
* Bugfix: Issue#224 PHP 8.1 ArrayAccess Issue by @bbanuri in https://github.com/podio-community/podio-php/pull/229
* Bugfix: PodioLogger.php fix by @mgithens in https://github.com/podio-community/podio-php/pull/225

[6.1.0](#v6.1.0) / 2023-01-19
==================
* Upgraded dependency Kint from 3.3 to 4.2.3

[6.0.2](#v6.0.2) / 2022-01-14
==================
* Bugfix: In some cases errors where raised, instead of defined exceptions if preconditions for save operations were missing (see [#213](https://github.com/podio-community/podio-php/issues/213)).
* Bugfix: File upload was broken ([#215](https://github.com/podio-community/podio-php/issues/215))

[6.0.1](#v6.0.1) / 2021-09-24
==================
* Bugfix: Turn off Guzzle HTTP errors, $podio_client->request handles 4xx and 5xx errors ([#211](https://github.com/podio-community/podio-php/issues/211))

[6.0.0](#v6.0.0) / 2021-08-23
==================
* BREAKING CHANGE: Drop support for PHP 5.x and 7.0/7.1/7.2
* Support PHP 8.0
* Use Guzzle HTTP client abstraction - now this falls back to PHP streams when curl is not available.
* Added get_item_values call (#193, thanks @dougblackjr)
* Replace optional kdyby/curl-ca-bundle by composer/ca-bundle (#200)

5.1.0 / 2020-07-15
==================
* Bugfix: Assure $podio_client->set_debug(true) performs debug output (with Kint) in non-cli setting.
* Doc: More thorough quick start guide in README.md (#190)
* Bugfix: Force HTTP 1.1 to prevent broken requests/file uploads (#191)

5.0.0 / 2020-03-10
==================

* Using composer for Kint dependency instead of copied files
* Add PodioTagItemField type
* Feature: Constant time PodioCollection access
* Adding filter API missing file_count parameter
* Add scope to PodioOAuth
* Bugfix: rate limit header parsing (#81)


4.4.0 / 2019-06-02
==================

* This is the first release under the new package name <strong>podio-community/podio-php</strong>.
It contains several fixes and minor improvements and should generally be backwards compatible to podio/podio-php v4.3.0.
* Several fixes and improvements: https://github.com/podio-community/podio-php/compare/4.3.0...v4.4.0


4.3.0 / 2015-09-30
==================

* Add support for Flows (https://developers.podio.com/doc/flows)


4.2.0 / 2015-07-02
==================

* Add `update_reference` and `count` to `PodioTask`
* Create `PodioVoting`
* Add low memory file fetch
* Verify TLS certificates
* Minor bug fixes


4.1.0 / 2015-06-16
==================

* Fix `PodioFile` `get_raw` concatenation
* Fix user model `mail` return value
* Add votes property and support for options when getting item
* Add missing properties to Comment model
* Add description to space model
* Make upload function compatible with `PHP 5.6`
* Add activation method for platform
* Add search method for platform
* Add method for org bootstrap for platform


4.0.2 / 2014-09-29
==================

* Minor bugfixes


4.0.1 / 2014-07-17
==================

* Minor bugfixes
* Make `authenticate_with_password` actually work
* Support image downloads at different sizes


4.0.0 / 2014-05-14
==================

* Introduced PodioCollection to make it easier to work with collections. Removed field and related methods from * PodioItem and PodioApp objects. Use the new array access interface instead.
* Made Podio*Itemfield objects more intuitive to work with
* Unit tests added for PodioCollection (and subclasses), PodioObject and Podio*ItemField classes
* Improved debugging options and added Kint for debugging
* Bug fixed: Handle GET/DELETE urls with options properly.
* Made __attributes and __properties private properties of PodioObject instances to underline that they shouldn’t be used


3.0.0 / 2014-01-31
==================

* Add options to bulk delete


2.0.0 / 2012-08-28
==================

* ¯\_(ツ)_/¯
