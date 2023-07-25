<?php
namespace MageMango\ProductReviewImage\ViewModel;

use Magento\Review\Model\ReviewFactory;

class ProductReviewImage implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var ReviewFactory
     */
    public $reviewFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * ProductReviewImage constructor.
     *
     * @param ReviewFactory $reviewFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        ReviewFactory $reviewFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->reviewFactory = $reviewFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * Get Media URL.
     *
     * @param string $path
     * @return string
     */
    public function getMediaUrl($path)
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $path;
    }

    /**
     * Review Data
     *
     * @param int $reviewId
     * @return Review
     */
    public function getReviewData($reviewId)
    {
        try {
            $review = $this->reviewFactory->create()->load($reviewId);
        } catch (LocalizedException $exception) {
            throw new LocalizedException(__($exception->getMessage()));
        }
        return $review;
    }
}
