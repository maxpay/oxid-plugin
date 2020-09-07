<?php
/**
 * This file is part of OXID Maxpay module.
 */

namespace Maxpay\MaxpayModule\Core;

/**
 * Maxpay config class.
 */
class Config
{
    /**
     * Maxpay module id.
     *
     * @var string
     */
    protected $maxpayId = 'maxpay';

    /**
     * Maxpay host.
     *
     * @var string
     */
    protected $maxpayHost = 'https://hpp.maxpay.com/hpp';

    /**
     * Please do not change this place.
     * It is important to guarantee the future development of this OXID eShop extension and to keep it free of charge.
     * Thanks!
     *
     * @var array Partner codes based on edition
     */
    protected $partnerCodes = array(
        'EE' => 'OXID_Cart_EnterpriseECS',
        'PE' => 'OXID_Cart_ProfessionalECS',
        'CE' => 'OXID_Cart_CommunityECS',
        'SHORTCUT' => 'Oxid_Cart_ECS_Shortcut'
    );

    /**
     * Return Maxpay module id.
     *
     * @return string
     */
    public function getModuleId(): string
    {
        return $this->maxpayId;
    }

    /**
     * Sets Maxpay host.
     *
     * @param string $maxpayHost
     */
    public function setMaxpayHost(string $maxpayHost): void
    {
        $this->maxpayHost = $maxpayHost;
    }

    /**
     * Returns Maxpay host.
     *
     * @return string
     */
    public function getMaxpayHost(): string
    {
        return $this->maxpayHost;
    }

    /**
     * Check if sandbox mode is enabled.
     *
     * @return bool
     */
    public function isSandboxEnabled(): bool
    {
        return $this->getParameter('blMaxpaySandboxMode');
    }

    /**
     * Get shop url.
     * 
     * @param bool $admin if admin
     * @return string
     */
    public function getShopUrl($admin = null): string
    {
        return \OxidEsales\Eshop\Core\Registry::getConfig()->getCurrentShopUrl($admin);
    }

    /**
     * Wrapper to get language object from registry.
     *
     * @return \OxidEsales\Eshop\Core\Language
     */
    public function getLang(): \OxidEsales\Eshop\Core\Language
    {
        return \OxidEsales\Eshop\Core\Registry::getLang();
    }

    /**
     * Please do not change this place.
     * It is important to guarantee the future development of this OXID eShop extension and to keep it free of charge.
     * Thanks!
     *
     * @return string partner code.
     */
    public function getPartnerCode(): string
    {
        $facts = new \OxidEsales\Facts\Facts();
        $key = $this->isShortcutPayment() ? self::PARTNERCODE_SHORTCUT_KEY : $facts->getEdition();

        return $this->partnerCodes[$key];
    }

    /**
     * Returns active shop id.
     *
     * @return integer
     */
    protected function getShopId(): int
    {
        return \OxidEsales\Eshop\Core\Registry::getConfig()->getShopId();
    }

    /**
     * Returns oxConfig instance.
     *
     * @return \OxidEsales\Eshop\Core\Config
     */
    protected function getConfig(): \OxidEsales\Eshop\Core\Config
    {
        return \OxidEsales\Eshop\Core\Registry::getConfig();
    }

    /**
     * Retrieve apropriate (sandbox/prod) publick key.
     * @return string
     */
    public function getPublicKey(): string
    {
        if ($this->isSandboxEnabled()) {
            $publicKey = $this->getParameter('sMaxpayTestPublicKey');
        } else {
            $publicKey = $this->getParameter('sMaxpayPublickKey');
        }

        return $publicKey;
    }
    
    /**
     * Retrieve apropriate (sandbox/prod) private key.
     * @return string
     */
    public function getPrivateKey(): string
    {
        if ($this->isSandboxEnabled()) {
            $privateKey = $this->getParameter('sMaxpayTestPrivateKey');
        } else {
            $privateKey = $this->getParameter('sMaxpayPrivateKey');
        }

        return $privateKey;
    }
    
    /**
     * Returns module config parameter value.
     *
     * @param string $paramName Parameter name.
     *
     * @return mixed
     */
    public function getParameter($paramName)
    {
        return \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam($paramName);
    }
}
