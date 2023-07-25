<?php
namespace MageMango\ProductReviewImage\Plugin\Magento\Review\Controller\Product;

class Post
{
    public function afterExecute(
        \Magento\Review\Controller\Product\Post $subject,
        $result
    ) {
        $data = $subject->getRequest()->getPostValue();
        $cdate = date('Ymd');

        $postData = [];
        foreach($data['image_video'] as $getData)
        {
            $uniqueNum = rand(0,9999);
            $postData[] = $uniqueNum . "_" . $cdate . "_" . $getData;
        }

        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/data.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info(print_r($postData, true));

        return $result;
    }
}