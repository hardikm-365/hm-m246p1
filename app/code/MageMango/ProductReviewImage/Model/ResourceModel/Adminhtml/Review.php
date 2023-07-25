<?php
namespace MageMango\ProductReviewimage\Model\ResourceModel\Adminhtml;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Review\Model\ReviewFactory;

/**
 * Review resource model
 */
class Review extends \Magento\Review\Model\ResourceModel\Review
{
    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_uploaderFactory;

    /**
     * @var ReviewFactory
     */
    private $reviewFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $_file;

    /**
     * @var \Magento\Framework\Filesystem 
     */
    protected $_fileSystem;

    /**
     * Review constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Review\Model\RatingFactory $ratingFactory
     * @param \Magento\Review\Model\ResourceModel\Rating\Option $ratingOptions
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $_uploaderFactory
     * @param \Magento\Framework\Filesystem\Driver\File $file
     * @param ReviewFactory $reviewFactory
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Review\Model\ResourceModel\Rating\Option $ratingOptions,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $_uploaderFactory,
        \Magento\Framework\Filesystem\Driver\File $file,
        ReviewFactory $reviewFactory,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->_uploaderFactory = $_uploaderFactory;
        $this->_file = $file;
        $this->reviewFactory = $reviewFactory;
        $this->request = $request;
        $this->_fileSystem = $filesystem;
        parent::__construct($context, $date, $storeManager, $ratingFactory, $ratingOptions, $connectionName = null);
    }

    /**
     * Perform actions after object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        $post = $this->request->getPost();
        $connection = $this->getConnection();
        $cdate = date('Ymd');

        $data = [];

        if (isset($post["review_image_hidden"]) && $post["review_image_hidden"]!='') {
            $folder=$this->_fileSystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath('advanceproductreview/');

            $review = $this->reviewFactory->create()->load($object->getId());
            $reviewexp = explode(",", $review['image']);
            $str = $post["review_image_hidden"];
            $strData = rtrim($str, ',');
            $strDataExp = explode(",", $strData);

            foreach ($strDataExp as $strDataExpData) {
                if ($this->_file->isExists($folder . $strDataExpData)) {
                    $this->_file->deleteFile($folder . $strDataExpData);
                }
            }
            $data = array_diff($reviewexp, $strDataExp);
        }
        if ($post["review_image_hidden"] == '') {
            $review = $this->reviewFactory->create()->load($object->getId());
            $data[] = $review['image'];
        }
        $imageArr = implode(",", $data);
        /**
         * save detail
         */
        $detail = [
            'title' => $object->getTitle(),
            'detail' => $object->getDetail(),
            'nickname' => $object->getNickname(),
            'image' => $imageArr
        ];
        $select = $connection->select()->from($this->_reviewDetailTable, 'detail_id')->where('review_id = :review_id');
        $detailId = $connection->fetchOne($select, [':review_id' => $object->getId()]);

        if ($detailId) {
            $condition = ["detail_id = ?" => $detailId];
            $connection->update($this->_reviewDetailTable, $detail, $condition);
        } else {
            $detail['store_id'] = $object->getStoreId();
            $detail['customer_id'] = $object->getCustomerId();
            $detail['review_id'] = $object->getId();
            $connection->insert($this->_reviewDetailTable, $detail);
        }

        /**
         * save stores
         */
        $stores = $object->getStores();
        if (!empty($stores)) {
            $condition = ['review_id = ?' => $object->getId()];
            $connection->delete($this->_reviewStoreTable, $condition);

            $insertedStoreIds = [];
            foreach ($stores as $storeId) {
                if (in_array($storeId, $insertedStoreIds)) {
                    continue;
                }

                $insertedStoreIds[] = $storeId;
                $storeInsert = ['store_id' => $storeId, 'review_id' => $object->getId()];
                $connection->insert($this->_reviewStoreTable, $storeInsert);
            }
        }

        // reaggregate ratings, that depend on this review
        $this->_aggregateRatings($this->_loadVotedRatingIds($object->getId()), $object->getEntityPkValue());

        return $this;
    }
}
