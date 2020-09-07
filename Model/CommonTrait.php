<?php
/**
 * This file is part of OXID Maxpay module.
 */

namespace Maxpay\MaxpayModule\Model;

/**
 * Trait Common Trait class.
 */
trait CommonTrait
{
    /**
     * @var \Maxpay\MaxpayModule\Core\Logger
     */
    protected $logger = null;
    
    /**
     * Maxpay service.
     *
     * @var \Maxpay\MaxpayModule\Core\MaxpayService|null
     */
    protected $maxpayService = null;  
    
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
}
