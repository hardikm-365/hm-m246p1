<?php
namespace MageMango\PostCodeWildCard\Plugin;

use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate as Subject;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\RateQueryFactory;

class Tablerate
{
    /**
     * @var RateQueryFactory
     */
    protected $rateQueryFactory;

    /**
     * Tablerate constructor.
     *
     * @param RateQueryFactory $rateQueryFactory
     */
    public function __construct(
        RateQueryFactory $rateQueryFactory
    ) {
        $this->rateQueryFactory = $rateQueryFactory;
    }

    /**
     * After Get Rate.
     *
     * @param Subject $subject
     * @param array $result
     * @param RateRequest $request
     * @return mixed
     */
    public function afterGetRate(
        Subject $subject,
        $result,
        RateRequest $request
    ) {
        $connection = $subject->getConnection();

        $select = $connection->select()->from($subject->getMainTable());
        /** @var RateQuery $rateQuery */
        $rateQuery = $this->rateQueryFactory->create(['request' => $request]);

        $rateQuery->prepareSelect($select);
        $bindings = $rateQuery->getBindings();
        $bindings[':postcode'] = substr($bindings[':postcode'], 0, 2);
        $bindings[':postcode_prefix'] = substr($bindings[':postcode_prefix'], 0, 2);

        $result = $connection->fetchRow($select, $bindings);
        // Normalize destination zip code
        if ($result && $result['dest_zip'] == '*') {
            $result['dest_zip'] = '';
        }
        return $result;
    }
}
