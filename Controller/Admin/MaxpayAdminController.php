<?php
/**
 * This file is part of OXID Maxpay module.
 */
namespace Maxpay\MaxpayModule\Controller\Admin;

use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterRequestProcessedEvent;

/**
 * Base maxpay admin controller class.
 */
class MaxpayAdminController extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Maxpay service.
     *
     * @var \Maxpay\MaxpayModule\Core\MaxpayService|null
     */
    protected $maxpayService = null;  
    
    /**
     * @var \Maxpay\MaxpayModule\Core\Logger
     */
    protected $logger = null;
    
    public function __construct() {
        $this->getLogger();
        $this->getMaxpayService();
        parent::__construct();
    }
    
    /**
     * Return Maxpay logger.
     *
     * @return \Maxpay\MaxpayModule\Core\Logger
     */
    public function getLogger(): \Maxpay\MaxpayModule\Core\Logger
    {
        if (is_null($this->logger)) {
            $session = \OxidEsales\Eshop\Core\Registry::getSession();
            $this->logger = oxNew(\Maxpay\MaxpayModule\Core\Logger::class);
            $this->logger->setLoggerSessionId($session->getId());
        }

        return $this->logger;
    }
    
    /**
     * Retrieve Maxpay service.
     *
     * @return \Maxpay\MaxpayModule\Core\MaxpayService
     */
    public function getMaxpayService(): \Maxpay\MaxpayModule\Core\MaxpayService
    {
        if (is_null($this->maxpayService)) {
            $this->setMaxpayService(oxNew(\Maxpay\MaxpayModule\Core\MaxpayService::class));
        }

        return $this->maxpayService;
    }
    
    /**
     * Sets Maxpay service.
     *
     * @param \Maxpay\MaxpayModule\Core\MaxpayService $maxpayService
     * @return void
     */
    public function setMaxpayService(\Maxpay\MaxpayModule\Core\MaxpayService $maxpayService): void
    {
        $this->maxpayService = $maxpayService;
    }
    
    /**
     * Executes method (creates class and then executes). Returns executed
     * function result.
     *
     * @param string $sFunction name of function to execute
     * @throws \OxidEsales\Eshop\Core\Exception\SystemComponentException system component exception
     */
    public function executeFunction($sFunction)
    {
        if ($sFunction && !self::$_blExecuted) {
            if (method_exists($this, $sFunction)) {
                $this->$sFunction();
                self::$_blExecuted = true;
                $this->dispatchEvent(new AfterRequestProcessedEvent());
            } else {
                // was not executed on any level ?
                if (!$this->_blIsComponent) {
                    /** @var \OxidEsales\Eshop\Core\Exception\SystemComponentException $oEx */
                    $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\SystemComponentException::class);
                    $oEx->setMessage('ERROR_MESSAGE_SYSTEMCOMPONENT_FUNCTIONNOTFOUND' . ' ' . $sFunction);
                    $oEx->setComponent($sFunction);
                    throw $oEx;
                }
            }
        }
    }
}
