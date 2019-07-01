<?php

namespace Sduif\ListPriceRule\Model\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\CatalogRule\Pricing\Price\CatalogRulePrice;

class ProductListObserver implements ObserverInterface
{
    private $storeManager;
    private $resource;
    private $customerSession;
    private $dateTime;
    private $localeDate;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ) {
        $this->storeManager = $storeManager;
        $this->resource = $resourceConnection;
        $this->customerSession = $customerSession;
        $this->dateTime = $dateTime;
        $this->localeDate = $localeDate;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
                
        $collection = $observer->getEvent()->getCollection();
        if (!$collection->hasFlag('added_catalog_rule_data')) {

            $connection = $this->resource->getConnection();
            $store = $this->storeManager->getStore();
            $collection->getSelect()
                ->joinLeft(
                    ['catalog_rule' => $this->resource->getTableName('catalogrule_product_price')],
                    implode(' AND ', [
                        'catalog_rule.product_id = ' . $connection->quoteIdentifier('e.entity_id'),
                        $connection->quoteInto('catalog_rule.website_id = ?', $store->getWebsiteId()),
                        $connection->quoteInto(
                            'catalog_rule.customer_group_id = ?',
                            $this->customerSession->getCustomerGroupId()
                        ),
                        $connection->quoteInto(
                            'catalog_rule.rule_date = ?',
                            $this->dateTime->formatDate($this->localeDate->scopeDate($store->getId()), false)
                        ),
                    ]),
                    [CatalogRulePrice::PRICE_CODE => 'rule_price']
                );

            $collection->setFlag('added_catalog_rule_data', true);
        }

        return $this;        
    }
}
