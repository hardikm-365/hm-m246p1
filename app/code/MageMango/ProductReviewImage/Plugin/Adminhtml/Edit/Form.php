<?php
namespace MageMango\ProductReviewImage\Plugin\Adminhtml\Edit;

class Form extends \Magento\Review\Block\Adminhtml\Edit\Form
{
    /**
     * Before Set Form Plugin
     *
     * @param \Magento\Review\Block\Adminhtml\Edit\Form $object
     * @param array $form
     * @return array
     */
    public function beforeSetForm(\Magento\Review\Block\Adminhtml\Edit\Form $object, $form)
    {
        $review = $object->_coreRegistry->registry('review_data');
        $fieldset = $form->getElement('review_details');
        $fieldset->addType(
            'image',
            \MageMango\ProductReviewImage\Block\Adminhtml\Review\Helper\Image::class
        );
        $fieldset->addField(
            'image',
            'image',
            [
                'label' => __('Review Images & Video')
            ]
        );
        $fieldset->addField(
            'review_image_hidden',
            'hidden',
            [
                'name'      => 'review_image_hidden',
                'label'     => 'review_image_hidden',
                'class'     => 'review_image_hidden',
                'required'  => false,
            ]
        );
        $form->setValues($review->getData());
        return [$form];
    }
}
