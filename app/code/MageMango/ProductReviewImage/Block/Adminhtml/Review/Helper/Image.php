<?php
namespace MageMango\ProductReviewImage\Block\Adminhtml\Review\Helper;

use Magento\Store\Model\StoreManagerInterface;

class Image extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     * @var StoreManagerInterface
     */
    private $_storeManager;

    /**
     * Image constructor.
     *
     * @param StoreManagerInterface $storemanager
     */
    public function __construct(
        StoreManagerInterface $storemanager
    ) {
        $this->_storeManager = $storemanager;
    }

    /**
     * Get category name.
     *
     * @param  DataObject $row
     * @return string
     */
    public function getElementHtml()
    {
        // here you can write your code.
        $html = '';

        if ($this->getValue()) {
            $html = $this->getMediaImageHtml($this->getValue());
        }
        return $html;
    }

    /**
     * Get Media Image Html
     *
     * @param string $imageName
     * @return string
     */
    public function getMediaImageHtml($imageName)
    {
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );

        $Image = $this->getValue();
        $imageArr = explode(",", $this->getValue());
        $label = '"<label><span>Review Images and Video</span></label>"';
        $class = "'label admin__field-label'";
        $html='';
        $html .=  "<script>";
        $html .=  "require(['jquery'], function($){";
        $html .=  "$('.field-image').prepend(".$label.");";
        $html .=  "$('.field-image label').addClass(".$class.");";
        $html .=  "})";
        $html .=  "</script>";
        // @codingStandardsIgnoreStart
        foreach ($imageArr as $imageArrData) {
            $sep = '_';
            $firstsep = strstr($imageArrData, $sep, true);
            $mediaUrl = $mediaDirectory . "advanceproductreview/" . $imageArrData;
            $html .= "<div class='review-attachments' style='float: left;'><div class='review-media-value' style='float: left;'><div class='image item base-image' data-role='image'>";
            $html .=  "<div class='product-image-wrapper'>";
            $html .=  "<div class='reviewimagevideo'>";
            $html .=  "<img class='product-image' data-role='image-element' src='".$mediaUrl."' id='0' alt='Image'>";
            $html .=  "</div>";
            $html .=  "<div class='actions'>";
            $html .=  "<button type='button' class='action-remove action-remove-item-".$firstsep."' 'action-remove' value='".$mediaUrl."' data-role='delete-button' title='Delete image' id='button'>";
            $html .=  "<span>Delete image</span>";
            $html .=  "</button>";
            $html .=  "</div>";
            $html .=  "</div>";
            $html .=  "</div>";
            $html .=  "</div>";
            $html .=  "</div>";
            $html .=  "<script>";
            $html .=  "require(['jquery'], function($){";
            $html .=  "$('.action-remove-item-".$firstsep."').on('click', function (e) { ";
            $html .=  "var fullPath = $(this).attr('value');";
            $html .=  "var filename = fullPath.replace(/^.*[\\\/]/, '');";
            $html .=  "$('#review_image_hidden').val($('#review_image_hidden').val()  + filename + ',');";
            $html .=  "$(e.target).parent().parent().parent().remove();";
            $html .=  "})";
            $html .=  "})";
            $html .=  "</script>";
            //$html .= "<img src='".$mediaUrl."' height='250px' width='250px' style='margin-right:20px;'>";
        }
        // @codingStandardsIgnoreEnd
        return $html;
    }
}
