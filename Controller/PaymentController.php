<?php
/**
 * This file is part of OXID Maxpay module.
 */

namespace Maxpay\MaxpayModule\Controller;

/**
 * Payment class wrapper for Maxpay module.
 *
 * @mixin \OxidEsales\Eshop\Application\Controller\PaymentController
 */
class PaymentController extends PaymentController_parent
{
    /**
     * Detects if current payment must be processed by Maxpay and instead of standard validation
     * redirects to standard Maxpay dispatcher
     *
     * @return mixed
     */
    public function validatePayment()
    {
        $paymentId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('paymentid');
        $session = \OxidEsales\Eshop\Core\Registry::getSession();
        $basket = $session->getBasket();
        
        if ($paymentId === 'oxidmaxpay' && $basket->getBruttoSum()) {
            $session->setVariable('paymentid', 'oxidmaxpay');

            return 'maxpaystandarddispatcher?fnc=setCheckout';
        }

        return parent::validatePayment();
    }
}
