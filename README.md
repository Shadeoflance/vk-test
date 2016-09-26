# vk-test

This is sources for a test project. Main goal was to create a web-site for viewing, creating, editing and deleting some products.
Product properties
------------------
* ID
* Name
* Price
* Description
* Image URL

Functional requiremets
----------------------
* Create/Edit/Delete products
* View products
* Products sorting by ID or price


Performance requirements
------------------------
0. Items view page should be accessible in <500ms
0. Items list has to be able to contain 1 000 000 items or less
0. Items view page should be able to handle throughput of 1000 requests/min or more

Technologies
------------
PHP, MySQL, Memcached

How it was achieved
-------------------
Main problem, of cource, was the performance. Getting items directly from DB was only giving throughput of <300. So it was necessary to use memcached and store some items information.

Since items amount limitations are huge, paging had to be added.

Then the main task was to be able to get certain range of items sorted by ID or price from cache. My solution was to store an array of IDs for each sorting (kind of like indexes). The items themselves were stored, as expected, separately with ID as key.
But native arrays in PHP are not arrays, but hash-tables, which means they require much more memory, unlike usual arrays. The solution was to convert those arrays to string and treat them as byte arrays.

The only problem left is refreshing the cache. Obviously, the best way to refresh items is when they are deleted/edited. The items order indexes, currently, refreshed on items creation/deletion and on price change of an existing item. Index refresh is very time-consuming operation, but considering the conditions, we can allow it on such operations as Create/Delete and Edit of and item, since those are not supposed to be frequent.

An improvement that could be made is cache pre-warm on server start. Currently, an item is put in cache only on first request to it.
