<?php
namespace Custom\CustomOption\Controller\Adminhtml\Upload;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\App\RequestInterface;

class Image extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    /**
     * Image uploader
     * @var \Magento\Catalog\Model\ImageUploader
     */
    protected $imageUploader;
    protected $_filesystem;
    protected $uploaderFactory;
    
    public function __construct(
        Context $context,
        \Magento\Catalog\Model\ImageUploader $imageUploader,
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory
    ) {
        parent::__construct($context);
       $this->imageUploader = $imageUploader;
        $this->_filesystem = $filesystem;
        $this->uploaderFactory = $uploaderFactory;
    }

    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $fileId = [];
        $data = $this->getRequest()->getPostValue();
        $fileId['tmp_name'] = $data['tmp_name'];
        
        // $files = $this->getRequest()->getFiles();
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        // $logger->info($_FILES['product[options][0][values][0][image]']['name']);
        $logger->info(print_r($data,true));
        // $path = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('catalog/product/');
        // $uploader = $this->uploaderFactory->create(['fileId' => $fileId['tmp_name']]);
        // $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'zip', 'doc']);
        // // $uploader->setFilenamesCaseSensitivity(false);
        // $uploader->setAllowRenameFiles(true);
        // $logger->info("test");
        //  $logger->info($uploader['fileId']);
        // $result = $uploader->save($path);
        // // $imageId = $this->getRequest()->getPostValue();
        // $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom.log');
        // $logger = new \Zend_Log();
        // $logger->addWriter($writer);
        // $logger->info($imageId);
        try {
            $result = $this->imageUploader->saveFileToTmpDir($fileId['tmp_name']);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)
            ->setData($result);
        // return $result;
        // return true;
    }
    
}
