---
layout: default
active: changelog
---
# Release notes

## 4.0.1

* Minor bugfixes
* Make `authenticate_with_password` actually work
* Support image downloads at different sizes

## 4.0.0

* Introduced PodioCollection to make it easier to work with collections. Removed `field` and related methods from `PodioItem` and `PodioApp` objects. Use the new array access interface instead. [See details]({{site.baseurl}}/objects).
* [Made Podio\*Itemfield objects more intuitive to work with]({{site.baseurl}}/items)
* Unit tests added for `PodioCollection` (and subclasses), `PodioObject` and `Podio*ItemField` classes
* Improved debugging options and added Kint for debugging
* Bug fixed: [Handle GET/DELETE urls with options properly.](https://github.com/podio/podio-php/commit/f1f81c0c8ff4584827bf63b5f023f659e536de5c)
* Made `__attributes` and `__properties` private properties of `PodioObject` instances to underline that they shouldn't be used
