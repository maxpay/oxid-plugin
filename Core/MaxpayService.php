<?php
/**
 * This file is part of OXID Maxpay module.
 */

namespace Maxpay\MaxpayModule\Core;

use Maxpay\Scriney;

/**
 * Maxpay Service class.
 */
class MaxpayService
{
    const REFUND_SUCCESS_STATUS = 'success';
    const POSTBACK_STATUS_SUCCESS = 'success';
    
    /**
     * Maxpay Config.
     *
     * @var \Maxpay\MaxpayModule\Core\Config
     */
    protected $maxpayConfig = null;
    
    public function __construct() {
        $this->getMaxpayConfig();
    }

    /**
     * Maxpay config setter.
     *
     * @param \Maxpay\MaxpayModule\Core\Config $maxpayConfig
     * @return void
     */
    public function setMaxpayConfig(\Maxpay\MaxpayModule\Core\Config $maxpayConfig): void
    {
        $this->maxpayConfig = $maxpayConfig;
    }

    /**
     * Maxpay config getter.
     *
     * @return \Maxpay\MaxpayModule\Core\Config
     */
    public function getMaxpayConfig(): \Maxpay\MaxpayModule\Core\Config
    {
        if (is_null($this->maxpayConfig)) {
            $this->setMaxpayConfig(oxNew(\Maxpay\MaxpayModule\Core\Config::class));
        }

        return $this->maxpayConfig;
    }

    /**
     * Transaction refund.
     * @param \OxidEsales\Eshop\Application\Model\Order $order
     * @return array
     */
    public function refundTransaction(\OxidEsales\Eshop\Application\Model\Order $order): array
    {
        $result = [
            'message' => '',
            'status' => false,
        ];
        
        $scriney = new Scriney($this->getPublicKey(), $this->getPrivateKey());
                    
        $amount = number_format($order->getTotalOrderSum(), 2, '.', '');
        $currency = $order->getOrderCurrency();
        $response = $scriney->refund($order->getId(), $amount, $currency->name);

        if ($scriney->validateApiResult($response) && (strtolower($response['status']) == self::REFUND_SUCCESS_STATUS)) {
            $order->setOrderRefundStatus();
            $result['message'] = "Order #" . $order->getId() . ", full refund message: " . $response['message'];
            $result['status'] = true;
        } else {
            $result['message'] = "Order #" . $order->getId() . ", full refund failed (" . ($response['message'] ?? '') . ')';
            $result['status'] = false;
        }
        
        return $result;
    }
    
    /**
     * Process callback request from Maxpay service.
     * @param string $dataJson
     * @param array $headers
     * @return string Log message.
     * @throws \Exception
     */
    public function processPostback(string $dataJson, array $headers): string
    {
        $scriney = new Scriney($this->getPublicKey(), $this->getPrivateKey());
        
        try {
            if ($scriney->validateCallback($dataJson, $headers)) {
                
                $data = json_decode($dataJson, true);
                
                $this->retrieveCallbackParams($data);
                
                if (!($order = oxNew(\OxidEsales\Eshop\Application\Model\Order::class)->getMaxpayOrder($this->orderId))) {
                    return 'Postback request order id is not valid!';
                }
                
                if ($this->responseStatus == self::POSTBACK_STATUS_SUCCESS && $this->responseCode === 0) {
                    $order->markOrderAsPaid();
                } else {
                    $order->setOrderErrorStatus();
                }
                
                return "OrderID: {$this->orderId} | Status: {$this->responseStatus} | Code: {$this->responseCode} | TransactionID: {$this->transactionId} | Amount: {$this->amount}";
                
            } else {
                throw new \Exception("Postback request is not valid!");
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
    
    /**
     * Redirects to Maxpay external payment page.
     * @param array $data
     * @return void
     */
    public function redirect(array $data): void
    {
        
        $url = $this->maxpayConfig->getMaxpayHost()
        
    ?>
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <script type="text/javascript">
                function closethisasap() {
                    document.forms["redirectpost"].submit();
                }
            </script>
        </head>
        <body onload="closethisasap();">
        <form name="redirectpost" method="post" action="<?= $url; ?>">
            <?php
            if ( !is_null($data) ) {
                foreach ($data as $k => $v) {
                    echo '<input type="hidden" name="' . $k . '" value="' . $v . '"> ';
                }
            }
            ?>
        </form>
        </body>
        </html>
        <?php
        exit;
    }
    
    /**
     * Read callback params from postback response.
     * @param array $data
     * @return void
     */
    private function retrieveCallbackParams(array $data): void
    {
        $this->orderId          = $data['uniqueTransactionId'] ?? '';
        $this->transactionId    = $data['reference'] ?? '';
        $this->userId           = $data['uniqueUserId'] ?? '';
        $this->amount           = $data['totalAmount'] ?? 0;
        $this->responseStatus   = $data['status'] ?? '';
        $this->responseMessage  = $data['message'] ?? '';
        $this->responseCode     = $data['code'] ?? '';
    }
    
    /**
     * Retrieve public key.
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->maxpayConfig->getPublicKey();
    }
    
    /**
     * Retrieve private key.
     * @return string
     */
    public function getPrivateKey(): string
    {
        return $this->maxpayConfig->getPrivateKey();
    }
    
    /**
     * Retrieve current lang.
     * @return string
     */
    public function getLangCode(): string
    {
        return $this->maxpayConfig->getLang()->getLanguageAbbr();
    }
}
