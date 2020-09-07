<?php
/**
 * This file is part of OXID Maxpay module.
 */
namespace Maxpay\MaxpayModule\Controller;

/**
 * Maxpay Standard Checkout dispatcher class.
 */
class StandardDispatcher extends MaxpayFrontendController
{
    /**
     * Maxpay checkout processing.
     * @return string
     */
    public function setCheckout(): string
    {
        $session = \OxidEsales\Eshop\Core\Registry::getSession();
        $session->setVariable("maxpay", "1");
        
        try {
            $basket = $session->getBasket();
            $basket->setPayment("oxidmaxpay");
            $basket->onUpdate();
            $basket->calculateBasket(true);

        } catch (\OxidEsales\Eshop\Core\Exception\StandardException $excp) {
            return "basket";
        }
        
        return 'order';
    }
}
