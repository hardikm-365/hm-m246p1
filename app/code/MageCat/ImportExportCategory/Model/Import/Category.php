<?php
namespace MageCat\ImportExportCategory\Model\Import;

use Exception;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\ImportExport\Helper\Data as ImportHelper;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\Framework\Registry;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ResourceModel\Helper;
use Magento\ImportExport\Model\ResourceModel\Import\Data;

/**
 * Class Courses
 */
class Category extends AbstractEntity
{
    public const ENTITY_CODE = 'category';
    public const TABLE = 'learning_courses';
    public const ENTITY_ID_COLUMN = 'entity_id';

    /**
     * If we should check column names
     * @var bool
     */
    protected $needColumnCheck = true;

    /**
     * Need to log in import history
     * @var bool
     */
    protected $logInHistory = true;

    /**
     * Permanent entity columns.
     * @var string[]
     */
    protected $_permanentAttributes = [
        'entity_id'
    ];

    /**
     * Valids columns names
     * @var string[]
     */
    protected $validColumnNames = [
        'entity_id',
        'name',
        'is_active',
        'parent_id'
    ];

    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var Registry $registry
     */
    private $registry;
    
    /**
     * Courses constructor.
     *
     * @param JsonHelper $jsonHelper
     * @param ImportHelper $importExportData
     * @param Data $importData
     * @param ResourceConnection $resource
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param Registry $registry
     * @param Helper $resourceHelper
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     */
    public function __construct(
        JsonHelper $jsonHelper,
        ImportHelper $importExportData,
        Data $importData,
        ResourceConnection $resource,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        Registry $registry,
        Helper $resourceHelper,
        ProcessingErrorAggregatorInterface $errorAggregator
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->resource = $resource;
        $this->categoryFactory = $categoryFactory;
        $this->registry = $registry;
        $this->connection = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator = $errorAggregator;
        $this->initMessageTemplates();
    }

    /**
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return static::ENTITY_CODE;
    }

    /**
     * Get available columns
     *
     * @return array
     */
    public function getValidColumnNames(): array
    {
        return $this->validColumnNames;
    }

    /**
     * Row validation
     *
     * @param array $rowData
     * @param int $rowNum
     *
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum): bool
    {

        $name = $rowData['name'] ?? '';
    // $duration = (int) $rowData['duration'] ?? 0;

        if (!$name) {
            $this->addRowError('NameIsRequired', $rowNum);
        }

    // if (!$duration) {
    //     $this->addRowError('DurationIsRequired', $rowNum);
    // }

        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }

        $this->_validatedRows[$rowNum] = true;

        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }
/**
 * Init Error Messages
 */
    private function initMessageTemplates()
    {
        $this->addMessageTemplate(
            'NameIsRequired',
            __('The name cannot be empty.')
        );
        $this->addMessageTemplate(
            'DurationIsRequired',
            __('Duration should be greater than 0.')
        );
    }
    /**
     * Import data
     *
     * @return bool
     *
     * @throws Exception
     */
    protected function _importData(): bool
    {

        switch ($this->getBehavior()) {
            case Import::BEHAVIOR_DELETE:
                $this->deleteEntity();
                break;
            case Import::BEHAVIOR_REPLACE:
                $this->saveAndReplaceEntity();
                break;
            case Import::BEHAVIOR_APPEND:
                $this->saveAndReplaceEntity();
                break;
        }

        return true;
    }

    /**
     * Delete entities
     *
     * @return bool
     */
    private function deleteEntity(): bool
    {
        $rows = [];
        $bunch = $this->_dataSourceModel->getNextUniqueBunch($this->getIds());
       
        foreach ($bunch as $rowNum => $rowData) {
            $this->validateRow($rowData, $rowNum);

            if (!$this->getErrorAggregator()->isRowInvalid($rowNum)) {
                $rowId = $rowData['name'];
                $rows[] = $rowId;
            }

            if ($this->getErrorAggregator()->hasToBeTerminated()) {
                $this->getErrorAggregator()->addRowToSkip($rowNum);
            }
        }

        if ($rows) {
            return $this->deleteEntityFinish(array_unique($rows));
        }

        return false;
    }

    /**
     * Save and replace entities
     *
     * @return void
     */
    private function saveAndReplaceEntity()
    {
        $behavior = $this->getBehavior();
        $rows = [];
        $bunch = $this->_dataSourceModel->getNextUniqueBunch($this->getIds());

        // while ($bunch = $this->_dataSourceModel->getNextUniqueBunch($this->getIds())) {
            
            $entityList = [];

        foreach ($bunch as $rowNum => $row) {
                
            if (!$this->validateRow($row, $rowNum)) {
                    
                continue;
            }
                
            if ($this->getErrorAggregator()->hasToBeTerminated()) {
                $this->getErrorAggregator()->addRowToSkip($rowNum);

                continue;
            }

            $rowId = $row['name'];
            $rows[] = $rowId;
            $columnValues = [];
              
            foreach ($this->getAvailableColumns() as $columnKey) {
                $columnValues[$columnKey] = $row[$columnKey];
            }

            $entityList[$rowId][] = $columnValues;

            $this->countItemsCreated += (int) !isset($row['name']);
            $this->countItemsUpdated += (int) isset($row['name']);
        }
            
        if (Import::BEHAVIOR_REPLACE === $behavior) {
            
                // if ($rows && $this->deleteEntityFinish(array_unique($rows))) {
                    
                    $this->saveEntityFinish($entityList);
                // }
        } elseif (Import::BEHAVIOR_APPEND === $behavior) {
            $this->saveEntityFinish($entityList);
        }
        
        // }
    }
    /**
     * Save entities
     *
     * @param array $entityRow
     *
     * @return string
     */
    private function getParent(array $entityRow)
    {
        if (array_key_exists('parent_id', $entityRow)) {
           
            $parentId = $entityRow['parent_id'];
        } else {
            
            $parentId = 2;
        }
        return $parentId;
    }

    /**
     * Save entities
     *
     * @param array $entityRow
     *
     * @return string
     */
    private function getIsActive(array $entityRow)
    {
        if (array_key_exists('is_active', $entityRow)) {
           
            if ($entityRow['is_active'] == 1) {
                $isActive = true;
            } else {
                $isActive = false;
            }
        } else {
            $isActive = true;
        }
        return $isActive;
    }

    /**
     * Save entities
     *
     * @param array $entityData
     *
     * @return bool
     */
    private function saveEntityFinish(array $entityData): bool
    {
    
        if ($entityData) {
            foreach ($entityData as $entityRows) {

                $parentId = 2;
               
                $parentCategory = $this->categoryFactory->create()->load($parentId);
                $category = $this->categoryFactory->create();
            
                foreach ($entityRows as $entityRow) {

                    $parentId = $this->getParent($entityRow);
                    $isActive = $this->getIsActive($entityRow);
                    $behavior = $this->getBehavior();
                    if (Import::BEHAVIOR_REPLACE === $behavior) {
                        $collection = $category->getCollection()->addAttributeToFilter('parent_id', $parentId);
                        $cate = $collection->addAttributeToFilter('entity_id', $entityRow['entity_id'])->getFirstItem();
                        
                        $parentCategory = $this->categoryFactory->create()->load($parentId);
                            $category->load($entityRow['entity_id']);
                            $category->setPath($parentCategory->getPath()."/".$entityRow['entity_id'])
                            ->setParentId($parentId)
                            ->setName($entityRow['name'])
                            ->setStoreId(0)
                            ->setIsActive($isActive);
                            $category->save();
                        
                    } else {
                        $collection = $category->getCollection()->addAttributeToFilter('parent_id', $parentId);
                        $cate = $collection->addAttributeToFilter('name', $entityRow['name'])->getFirstItem();

                        $parentCategory = $this->categoryFactory->create()->load($parentId);
                        if (!$cate->getId()) {
                            
                                $category->setPath($parentCategory->getPath())
                                ->setParentId($parentId)
                                ->setName($entityRow['name'])
                                ->setStoreId(0)
                                ->setIsActive($isActive);
                                $category->save();
                        }
                    }
                }
            }
            return true;
            
        }
        return false;
    }

    /**
     * Delete entities
     *
     * @param array $entityIds
     *
     * @return bool
     */
    private function deleteEntityFinish(array $entityIds): bool
    {
        
        if ($entityIds) {
            try {
                
                foreach ($entityIds as $entityId) {
                    $categories = $this->categoryFactory->create();
                    $cate1 = $categories->getCollection()->addAttributeToFilter('name', $entityId)->getFirstItem();
                   // $this->registry->register("isSecureArea", true);
                    $cate1->delete();
                   
                    $this->countItemsDeleted += (int) isset($entityId);
                }
                return true;
            } catch (Exception $e) {
                
                return false;
            }
        }

        return false;
    }

    /**
     * Get available columns
     *
     * @return array
     */
    private function getAvailableColumns(): array
    {
        return $this->validColumnNames;
    }
}
