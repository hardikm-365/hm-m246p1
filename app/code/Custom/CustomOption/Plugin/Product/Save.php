<?php

namespace Custom\CustomOption\Plugin\Product;

use Magento\Catalog\Controller\Adminhtml\Product\Save as SavePost;
use Magento\Framework\App\RequestInterface;

class Save
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    public function __construct(
        RequestInterface $request
    )
    {
        $this->_request = $request;
    }

    public function beforeExecute(SavePost $subject)
    {
        $post = $this->_request->getPost();

        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom1before.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info("tessss");
        $logger->info(print_r($post, true));
    }
}