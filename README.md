# Magento 2 | Access Catalog Price Rule information on product list page

This is a simple module for Magento 2 that'll allow you to access catalog price rule prices on the product list.

## Installation

* Add this plugin to your store
* (Optional) Enable maintenance mode **php bin/magento maintenance:enable**
* Flush cache **php bin/magento cache:flush**
* Run upgrade **php bin/magento setup:upgrade**
* Compile (Production) **php bin/magento setup:di:compile**
* Redeploy static content **php bin/magento setup:static-content:deploy**
* (Optional) Disable  maintenance mode **php bin/magento maintenance:disable**

## Usage

In your list.phtml you can now access price rule information through **$_product->getCatalogRulePrice()**

For Example:

~~~~
<?php  echo ($_product->getCatalogRulePrice() ? 'has catalog price rule' : 'regular'); ?>
~~~~