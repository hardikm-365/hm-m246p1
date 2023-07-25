<?php
namespace MageMango\CustomShippingAmount\Plugin\Model;

class Shipping
{
    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory
     */
    private $rateErrorFactory;

    /**
     * Shipping constructor.
     *
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     */
    public function __construct(
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
    ) {
        $this->rateErrorFactory = $rateErrorFactory;
    }

    /**
     * after plugin
     *
     * @param \Magento\Shipping\Model\Shipping $subject
     * @param \Magento\Shipping\Model\Rate\Result] $result
     * @return void
     */
    public function afterCollectRates(\Magento\Shipping\Model\Shipping $subject, $result)
    {
        $result = $subject->getResult();
        $rates = $result->getAllRates();
        $result->reset();
        foreach ($rates as $rate) {
            $restrict = false;
            if ($rate->getCarrier() == 'customshipping') {
                $restrict = true;
                $this->setError($result, $rate);
            }
            if (!$restrict) {
                $result->append($rate);
            }
        }
        return $subject;
    }

    /**
     * set error message
     * @param \Magento\Shipping\Model\Rate\Result $result
     * @param \Magento\Quote\Model\Quote\Address\RateResult\Method $rate
     *
     * @return bool
     */
    private function setError($result, $rate)
    {
        $errorMessage = __('Sorry, shipping method is restricted');

        if ($rate !== null && $errorMessage) {
            $error = $this->rateErrorFactory->create();
            $error->setCarrier($rate->getCarrier());
            $error->setCarrierTitle($rate->getCarrierTitle().' ('.$rate->getMethodTitle().')');
            $error->setErrorMessage($errorMessage);

            $result->append($error);
        }
    }
}