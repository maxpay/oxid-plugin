<?php
/**
 * This file is part of OXID Maxpay module.
 */

namespace Maxpay\MaxpayModule\Model;

/**
 * Maxpay oxOrder class
 *
 * @mixin \OxidEsales\Eshop\Application\Model\Order
 */
class Order extends Order_parent
{
    use CommonTrait;
    
    /**
     * Maxpay order information
     *
     * @var \Maxpay\MaxpayModule\Model\Order
     */
    protected $maxpayOrder = null;
    
    /** Transaction is processing successfully. */
    const MAXPAY_PAYMENT_PROCESSING = 'Awaiting Maxpay payment';
    
    /** Transaction is finished successfully. */
    const MAXPAY_PAYMENT_COMPLETED = 'Maxpay payment completed';
    
    /** Transaction is finished successfully. */
    const MAXPAY_PAYMENT_REFUNDED = 'Maxpay payment refunded';
    
    /** Transaction is not finished or failed. */
    const MAXPAY_PAYMENT_ERROR = 'Maxpay payment failed';    

    public function __construct() {
        $this->getLogger();
        $this->logger->setTitle('Order status update');
        parent::__construct();
    }
    
    /**
     * Loads order associated with current Maxpay order.
     *
     * @return bool
     */
    public function loadMaxpayOrder(): bool
    {
        $orderId = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable("sess_challenge");

        if ($orderId === null) {
            $orderId = \OxidEsales\Eshop\Core\UtilsObject::getInstance()->generateUID();
            $this->setId($orderId);
            $this->save();
            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("sess_challenge", $orderId);
        }

        return $this->load($orderId);
    }

    /**
     * Updates order number.
     *
     * @return void
     */
    public function maxpayUpdateOrderNumber(): void
    {
        if ($this->oxorder__oxordernr->value) {
            oxNew(\OxidEsales\Eshop\Core\Counter::class)->update($this->_getCounterIdent(), $this->oxorder__oxordernr->value);
        } else {
            $this->_setNumber();
        }
    }
    
    /**
     * Retrieve order status.
     * @return string
     */
    public function getOrderStatus(): string
    {
        return $this->oxorder__oxtransstatus->value ?? '';
    }

    /**
     * Update order oxpaid to current time.
     * @return void
     */
    public function markOrderAsPaid(): void
    {
        parent::_setOrderStatus(self::MAXPAY_PAYMENT_COMPLETED);

        $db = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $utilsDate = \OxidEsales\Eshop\Core\Registry::getUtilsDate();
        $date = date('Y-m-d H:i:s', $utilsDate->getTime());

        $query = 'update oxorder set oxpaid=? where oxid=?';
        $db->execute($query, array($date, $this->getId()));

        //updating order object
        $this->oxorder__oxpaid = new \OxidEsales\Eshop\Core\Field($date);
    }
    
    /**
     * Update order status to Processing.
     * @param string $message
     * @return void
     */
    public function setOrderSuccessStatus(string $message = ''): void
    {
        $this->logger->log($message);
        parent::_setOrderStatus(self::MAXPAY_PAYMENT_PROCESSING);
    }
    
    /**
     * Update order status to Error.
     * @param string $message
     * @return void
     */
    public function setOrderErrorStatus(string $message = ''): void
    {
        $this->logger->log($message);
        parent::_setOrderStatus(self::MAXPAY_PAYMENT_ERROR);
    }
    
    /**
     * Update order status to Refund.
     * @return void
     */
    public function setOrderRefundStatus(): void
    {
        parent::_setOrderStatus(self::MAXPAY_PAYMENT_REFUNDED);
    }

    /**
     * Returns Maxpay order object.
     *
     * @param string $oxId
     *
     * @return \Maxpay\MaxpayModule\Model\Order|null
     */
    public function getMaxpayOrder($oxId = null)
    {
        if (is_null($this->maxpayOrder)) {
            $orderId = is_null($oxId) ? $this->getId() : $oxId;
            $order = oxNew(\Maxpay\MaxpayModule\Model\Order::class);
            $order->load($orderId);
            $this->maxpayOrder = $order;
        }

        return $this->maxpayOrder;
    }
}
