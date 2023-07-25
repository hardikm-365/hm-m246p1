<?php
namespace MageCat\ImportExportCategory\Plugin;

use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\ImportExport\Model\ExportFactory;

class Export
{
  
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var SessionManagerInterface
     */
    private $export;

    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * OrderManagement constructor.
     *
     * @param FileFactory $fileFactory
     * @param ExportFactory $export
     * @param SessionManagerInterface|null $sessionManager
     */
    public function __construct(
        FileFactory $fileFactory,
        ExportFactory $export,
        SessionManagerInterface $sessionManager = null
    ) {
        $this->fileFactory = $fileFactory;
        $this->export = $export;
        $this->sessionManager = $sessionManager
            ?? \Magento\Framework\Session\SessionManagerInterface::class;
    }

    /**
     * Before plugin for execute foe export csv
     *
     * @param \Magento\ImportExport\Controller\Adminhtml\Export\Export $subject
     */
    public function beforeExecute(\Magento\ImportExport\Controller\Adminhtml\Export\Export $subject)
    {
        $params = $subject->getRequest()->getParams();
        $entity = $params['entity'];

        if ($entity == 'catalog_category') {
            $model = $this->export->create();
            $model->setData($subject->getRequest()->getParams());
            $this->sessionManager->writeClose();
            
            return $this->fileFactory->create(
                $model->getFileName(),
                $model->export(),
                \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                $model->getContentType()
            );
        }
    }
}
