<?php
/**
 * This file is part of OXID Maxpay module.
 */

namespace Maxpay\MaxpayModule\Model;

use Maxpay\Lib\Util\SignatureHelper;

/**
 * Payment gateway manager.
 * Checks and sets payment method data, executes payment.
 *
 * @mixin \OxidEsales\Eshop\Application\Model\PaymentGateway
 */
class PaymentGateway extends PaymentGateway_parent
{
    use CommonTrait;
    
    public function __construct() {
        $this->getLogger();
        $this->getMaxpayService();
        $this->logger->setTitle('Order payment proceed');
        parent::__construct();
    }
    
    /**
     * Executes payment, returns true on success.
     *
     * @param float                            $amount Goods amount.
     * @param \Maxpay\MaxpayModule\Model\Order $order  User ordering object.
     *
     * @return bool
     */
    public function executePayment(float $amount, \Maxpay\MaxpayModule\Model\Order &$order): bool
    {
        $success = parent::executePayment($amount, $order);
        $session = \OxidEsales\Eshop\Core\Registry::getSession();

        if ( ($session->getVariable('paymentid') == 'oxidmaxpay')
             || ($session->getBasket()->getPaymentId() == 'oxidmaxpay')
        ) {
            $success = $this->doCheckoutPayment();
        }

        return $success;
    }

    /**
     * Executes "DoCheckoutPayment" to Maxpay
     *
     * @return bool
     */
    public function doCheckoutPayment(): bool
    {
        $order = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
        $order->loadMaxpayOrder();
        $orderId = $order->getId();
        $session = \OxidEsales\Eshop\Core\Registry::getSession();

        try {
            
            $lang = $this->maxpayService->getLangCode();
            
            $basket = $session->getBasket();
            $user = $this->getUser();
            $userDetails = $user->getDetails();

            $params = [
                'key' => $this->maxpayService->getPublicKey(),
                'uniqueuserid' => $userDetails['customer_id'],
                'email' => $userDetails['email'],
                'firstname' => $userDetails['firstname'],
                'lastname' => $userDetails['lastname'],
                'locale' => $lang . '-' . strtoupper($lang),
                'city' => $userDetails['city'],
                'zip' => $userDetails['zip'],
                'address' => $userDetails['address'],
                'country' => $userDetails['country'],
                'phone' => $userDetails['phone'],
            ];
            
            if ($order && $orderId) {
                $order->maxpayUpdateOrderNumber();
                
                $params['uniqueTransactionId'] = $orderId;
                $params['customProduct'] = '[' . json_encode([
                    'productType' => 'fixedProduct',
                    'productId'   => $orderId,
                    'productName' => 'Order id #' . $orderId,
                    'currency'    => $basket->getBasketCurrency()->name,
                    'amount'      => $basket->getPriceForPayment(),
                ]) . ']';

                $params['signature'] = (new SignatureHelper())->generateForArray($params, $this->maxpayService->getPrivateKey(), true);
                $params['customProduct'] = htmlspecialchars($params['customProduct']);
            }

        } catch (\OxidEsales\Eshop\Core\Exception\StandardException $excp) {
            return false;
        }
        $this->logger->log('Redirect to maxpay payment processing');
        $this->maxpayService->redirect($params);

        return true;
    }
}
