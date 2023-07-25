<?php
namespace MageMango\CustomShippingAmount\Controller\Amount;

use Magento\Framework\App\Action\HttpGetActionInterface;

class Index implements HttpGetActionInterface
{
    public function __construct(
        \Magento\Checkout\Model\Session $session
    ) {
        $this->quote = $session->getQuote();
    }
    public function execute()
    {
        $this->quote->getShippingAddress()->setShippingType("Premium");
    }
}