<?php
namespace MageCat\ImportExportCategory\Model\Export;

use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\ImportExport\Model\Import;
use Magento\Store\Model\Store;
use Magento\Catalog\Model\Category as ProductEntity;

/**
 * Class Category
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Category extends \Magento\ImportExport\Model\Export\Entity\AbstractEntity
{

    /**
     * Collection factory of category
     *
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;


    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * Attributes that should be exported
     *
     * @var string[]
     */
    protected $_bannedAttributes = ['name'];

    /**
     * Array of supported product types as keys with appropriate model object as value.
     *
     * @var array
     */
    protected $_productTypeModels = [];

    /**
     * Product collection
     *
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_entityCollectionFactory;

    /**
     * Product collection
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $_entityCollection;

    /**
     * Header columns for export file
     *
     * @var array
     * @since 100.2.0
     */
    protected $_headerColumns = [];

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Attribute\Collection
     */
    protected $_attributeColFactory;




    /**
     * Map between import file fields and system fields/attributes
     *
     * @var array
     */
    protected $_fieldsMap = [
        'entity_id' => 'entity_id',
        'name' => 'category',
        'is_active' => 'is_active',
        'parent_id' => 'parent_id'
    ];

    /**
     * Map between import file fields and system fields/attributes
     *
     * @var string[]
     */

    protected $headers = [
        'entity_id',
        'category',
        'is_active',
        'parent_id'

    ];
    /**
     * Attributes codes which shows as date
     *
     * @var array
     * @since 100.1.2
     */
    protected $dateAttrCodes = [
        'custom_design_from',
        'custom_design_to'
    ];

    /**
     * Attributes codes which are appropriate for export and not the part of additional_attributes.
     *
     * @var array
     */
    protected $_exportMainAttrCodes = [
        'all_children',
        'available_sort_by',
        'children',
        'children_count',
        'custom_apply_to_products',
        'custom_design',
        'custom_design_from',
        'custom_design_to',
        'custom_layout_update',
        'custom_use_parent_settings',
        'default_sort_by',
        'description',
        'display_mode',
        'filter_price_range',
        'image',
        'include_in_menu',
        'is_active',
        'is_anchor',
        'landing_page',
        'name',
        'name',
        'url_key',
        'url_path',
    ];

    /**
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Eav\Model\Config $config
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\Option\CollectionFactory $optionColFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\Attribute\CollectionFactory $attributeColFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param array $dateAttrCodes
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Eav\Model\Config $config,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\Attribute\CollectionFactory $attributeColFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        array $dateAttrCodes = []
    ) {
        $this->_entityCollectionFactory = $collectionFactory;
        $this->_attributeColFactory = $attributeColFactory;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->dateAttrCodes = array_merge($this->dateAttrCodes, $dateAttrCodes);
        $this->_categoryFactory = $categoryFactory;
        parent::__construct($localeDate, $config, $resource, $storeManager);
    }
    /**
     * Custom fields mapping for changed purposes of fields and field names
     *
     * @param array $rowData
     *
     * @return array
     */
    protected function _customFieldsMapping($rowData)
    {
        $rowData1 = [];

        foreach ($this->_fieldsMap as $systemFieldName => $fileFieldName) {

            if (isset($rowData[$systemFieldName])) {

                $rowData1[$fileFieldName] = $rowData[$systemFieldName];

                unset($rowData[$systemFieldName]);
            }
        }

        return $rowData1;
    }

    /**
     * Custom headers mapping for changed field names
     *
     * @param array $rowData
     *
     * @return array
     */
    protected function _customHeadersMapping($rowData)
    {

        foreach ($rowData as $key => $fieldName) {
            if (isset($this->_fieldsMap[$fieldName])) {
                $rowData[$key] = $this->_fieldsMap[$fieldName];
            }
        }
        return $rowData;
    }

    /**
     * Get header columns
     *
     * @return string[]
     */
    public function _getHeaderColumns()
    {
        // $validAttributeCodes = $this->_getExportAttributeCodes();
        // return array_merge($this->_permanentAttributes, $validAttributeCodes);
        return $this->headers;
    }

    /**
     * Get entity collection
     *
     * @param bool $resetCollection
     * @return \Magento\Framework\Data\Collection\AbstractDb
     */
    protected function _getEntityCollection($resetCollection = false)
    {
        if ($resetCollection || empty($this->_entityCollection)) {
            $this->_entityCollection = $this->_entityCollectionFactory->create();
        }
        return $this->_entityCollection;
    }

    /**
     * Set page and page size to collection
     *
     * @param int $page
     * @param int $pageSize
     * @return void
     */
    protected function paginateCollection($page, $pageSize)
    {
        $this->_getEntityCollection()->setPage($page, $pageSize);
    }

    /**
     * Export process
     *
     * @return string
     */
    public function export()
    {

        $writer = $this->getWriter();
        $page = 0;
        while (true) {
            ++$page;
            $entityCollection = $this->_getEntityCollection(true);
            $entityCollection->setOrder('entity_id', 'asc');
            $entityCollection->setStoreId(Store::DEFAULT_STORE_ID);
            $this->_prepareEntityCollection($entityCollection);
            // $this->paginateCollection($page, $this->getItemsPerPage());
            if ($entityCollection->count() == 0) {

                break;
            }
            $exportData = $this->getExportData();
            unset($exportData[1]);

            if ($page == 1) {

                $writer->setHeaderCols($this->_getHeaderColumns());
            }
            foreach ($exportData as $dataRow) {
                $writer->writeRow($this->_customFieldsMapping($dataRow));
            }
            if ($entityCollection->getCurPage() >= $entityCollection->getLastPageNumber()) {
                break;
            }
        }

        return $writer->getContents();
    }

    /**
     * Category Data for Csv
     *
     * @param string|array $exportData
     * @return string|array
     */
    protected function getCategoryCsvData($exportData)
    {

        $result = [];
        $exportCategoryData = $exportData;

        $result[] = [
            'entity_id',
            'category',
            'is_active',
            'parent_id'
        ];
        foreach ($exportCategoryData as $csvData) {
            $result[] = [
                isset($csvData['entity_id']) ? $csvData['entity_id'] : '',
                isset($csvData['name']) ? $csvData['name'] : '',
                isset($csvData['is_active']) ? $csvData['is_active'] : '',
                isset($csvData['parent_id']) ? $csvData['parent_id'] : ''
            ];
        }

        return $result;
    }

    /**
     * Get export data for collection
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function getExportData()
    {
        $exportData = [];

        $isActive = true;
        $level = false;
        $sortBy = false;
        $pageSize = false;

        try {
            $collection = $this->_categoryCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->setStore($this->_storeManager->getStore()->getId());
            foreach ($collection as $index => $value) {
                $categoryIdProduct = $value->getEntityId();

                $categoryId = $categoryIdProduct;
                $category = $this->_categoryFactory->create()->load($categoryId);
                $exportData[$index] = $value->getData();
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return $exportData;
    }

    /**
     * Entity attributes collection getter.
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
     */
    public function getAttributeCollection()
    {
        return $this->_attributeColFactory->create();
    }

    /**
     * EAV entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'catalog_category';
    }
}
