---
layout: default
active: upgrade
---
# Upgrading from an older version

There are some backwards incompatible changes in this version of podio-php.

* PodioApp and PodioItem no longer have `field`, `add_field` and `remove_field` methods. [Instead use the new interface for working with collections]({{site.baseurl}}/objects).
* The way values for PodioItemField are accessed and modified have been greatly simplified. Any code that accessed `->values` will need to change. [See a full set of examples for all fields]({{site.baseurl}}/fields).
* You can no longer access `->__attributes` and `->__properties` on objects. These are internal variables and there are [much better ways to work with objects]({{site.baseurl}}/objects).
* `->fields` on PodioApp and PodioItem are no longer simple arrays of objects. They are instances of [PodioCollection]({{site.baseurl}}/objects).
