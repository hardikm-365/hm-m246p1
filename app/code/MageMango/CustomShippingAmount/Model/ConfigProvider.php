<?php
namespace MageMango\CustomShippingAmount\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * Get Config
     *
     * @return array[]
     */
    public function getConfig()
    {
        /*$configData = [{'id':1,'name':'test1'},{'id':2,'name':'test2'},{'id':3,'name':'test3'}];
        return [
            'shipping_amount' => $configData
        ];*/
        $additionalVariables['shipping_type'] = ['Express','Premium'];
        return $additionalVariables;
    }
}
