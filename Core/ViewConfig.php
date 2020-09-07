<?php
/**
 * This file is part of OXID Maxpay module.
 */

namespace Maxpay\MaxpayModule\Core;

/**
 * ViewConfig class wrapper for Maxpay module.
 *
 * @mixin \OxidEsales\Eshop\Core\ViewConfig
 */
class ViewConfig extends ViewConfig_parent
{
    /** @var null \Maxpay\MaxpayModule\Core\Config */
    protected $maxpayConfig = null;

    /**
     * Returns Maxpay config.
     *
     * @return \Maxpay\MaxpayModule\Core\Config
     */
    protected function getMaxpayConfig(): \Maxpay\MaxpayModule\Core\Config
    {
        if (is_null($this->maxpayConfig)) {
            $this->maxpayConfig = oxNew(\Maxpay\MaxpayModule\Core\Config::class);
        }

        return $this->maxpayConfig;
    }
    
    /**
     * Check if order has been already refunded.
     * @param string $orderId
     * @return string
     */
    public function isOrderRefunded(string $orderId): string
    {
        $order = oxNew(\OxidEsales\Eshop\Application\Model\Order::class)->getMaxpayOrder($orderId);
        return $order->getOrderStatus() === $order::MAXPAY_PAYMENT_REFUNDED;
    }
    
    /**
     * Check if order payment is completed.
     * @param string $orderId
     * @return string
     */
    public function isOrderCompleted(string $orderId): string
    {
        $order = oxNew(\OxidEsales\Eshop\Application\Model\Order::class)->getMaxpayOrder($orderId);
        return $order->getOrderStatus() === $order::MAXPAY_PAYMENT_COMPLETED;
    }
}
