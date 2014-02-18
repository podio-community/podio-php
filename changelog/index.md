---
layout: default
active: changelog
---
# Release notes
## 4.0.0

* Introduced PodioCollection to make it easier to work with collections. Removed `field` and related methods from `PodioItem` and `PodioApp` objects. Use the new array access interface instead.
* Some unit tests added for collections and items
* Improved debugging options added Kint for debugging
* Bug fixed: [Handle GET/DELETE urls with options properly.](https://github.com/podio/podio-php/commit/f1f81c0c8ff4584827bf63b5f023f659e536de5c)
