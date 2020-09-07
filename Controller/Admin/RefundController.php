<?php
/**
 * This file is part of OXID Maxpay module.
 */
namespace Maxpay\MaxpayModule\Controller\Admin;

/**
 * Refund class wrapper for Maxpay module
 */
class RefundController extends MaxpayAdminController
{
    /**
     * Order refund result status.
     * @var bool
     */
    private $refundResult = null;
    
    public function __construct()
    {
        parent::__construct();
        $this->logger->setTitle('Refund request');
    }
    
    /**
     * Render/ajax response functionality.
     * @return string
     */
    public function render(): string
    {
        if (is_bool($this->refundResult)) {
            return $this->ajaxRefundResponse();
        } else {
            return $this->defaultRender();
        }
    }
    
    /**
     * Ajax refund request response.
     * @return string
     */
    private function ajaxRefundResponse(): string
    {
        if ($this->refundResult) {
            $templateName = 'refund_success.tpl';
        } else {
            $templateName = 'refund_failed.tpl';
        }
        return $templateName;
    }
    
    /**
     * Default render functionality.
     * @return string
     */
    private function defaultRender(): string
    {
        parent::render();

        $this->_aViewData["sOxid"] = $this->getEditObjectId();
        if ($this->isMaxpayOrder()) {
            $this->_aViewData['oOrder'] = $this->getEditObject();
        } else {
            $this->_aViewData['sMessage'] = \OxidEsales\Eshop\Core\Registry::getLang()->translateString("MAXPAY_ONLY_PAYMENT");
        }

        return "order_maxpay.tpl";
    }

    /**
     * Returns editable order object.
     *
     * @return \Maxpay\MaxpayModule\Model\Order
     */
    public function getEditObject(): \Maxpay\MaxpayModule\Model\Order
    {
        $soxId = $this->getEditObjectId();
        if ($this->_oEditObject === null && isset($soxId) && $soxId != '-1') {
            $this->_oEditObject = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
            $this->_oEditObject->load($soxId);
        }

        return $this->_oEditObject;
    }
    
    /**
     * Refund fucntionality.
     * @return void
     */
    public function refund(): void
    {
        if (($_SERVER['REQUEST_METHOD'] === 'POST') && $this->isMaxpayOrder()) {
            
            $orderId = \oxRegistry::getConfig()->getRequestParameter('oxid', null);
        
            if ($orderId) {
                $order = oxNew(\OxidEsales\Eshop\Application\Model\Order::class)->getMaxpayOrder($orderId);
                
                if ($order->getOrderStatus() !== $order::MAXPAY_PAYMENT_REFUNDED) {
                    $result = $this->maxpayService->refundTransaction($order);
                    $this->logger->log($result['message']);
                    $this->refundResult = $result['status'];
                }
            }
        }
    }

    /**
     * Method checks is order was made with Maxpay module.
     *
     * @return bool
     */
    public function isMaxpayOrder(): bool
    {
        $active = false;

        $order = $this->getEditObject();
        if ($order && $order->getFieldData('oxpaymenttype') == 'oxidmaxpay') {
            $active = true;
        }

        return $active;
    }
}
