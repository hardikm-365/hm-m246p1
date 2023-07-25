<?php
namespace MageMango\ProductReviewImage\Controller\Index;

use Magento\Framework\Json\Helper\Data as JsonHelper;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $connection;
    protected $_mediaDirectory;
    protected $_fileUploaderFactory;
    public $_storeManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\ResourceConnection $connection,
        JsonHelper $jsonHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Result\PageFactory $pageFactory)
    {
        $this->_pageFactory = $pageFactory;
        $this->connection = $connection;
        $this->jsonHelper = $jsonHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_storeManager = $storeManager;
        return parent::__construct($context);
    }

    public function execute(){
        $_postData = $this->getRequest()->getPostValue();
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/image.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info(print_r($_postData, true));
    }
}