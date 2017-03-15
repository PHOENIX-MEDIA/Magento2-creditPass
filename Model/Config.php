<?php
/**
 * creditPass for Magento 2
 *
 * @category    Phoenix
 * @package     Phoenix_Creditpass
 * @copyright   Copyright (c) 2016 PHOENIX MEDIA GmbH (http://www.phoenix-media.eu)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Phoenix\Creditpass\Model;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Data constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Return the config value for the passed key (current store)
     *
     * @param string $key
     * @return string
     */
    protected function getConfig($key)
    {
        $path = 'creditpass/settings/' . $key;
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Return the config value for the passed key (current store)
     *
     * @param string $field
     * @return boolean
     */
    protected function getConfigFlag($field)
    {
        $path = 'creditpass/settings/' . $field;
        return $this->scopeConfig->isSetFlag($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check if the extension has been enabled in the system configuration
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getConfigFlag('active');
    }

    /**
     * Returns API URL
     *
     * @return string
     */
    public function getApiUrl()
    {
        return $this->getConfig('cp_url');
    }

    /**
     * Returns API Auth ID
     *
     * @return string
     */
    public function getApiAuthId()
    {
        return $this->getConfig('auth_id');
    }

    /**
     * Returns API Auth password
     *
     * @return string
     */
    public function getApiAuthPassword()
    {
        return $this->getConfig('auth_pw');
    }

    /**
     * Returns whether live mode is enabled.
     *
     * @return bool
     */
    public function isLiveMode()
    {
        return $this->getConfigFlag('live_mode');
    }

    /**
     * Return the allowed payment method codes
     *
     * @return array()
     */
    public function getAllowedPaymentMethods()
    {
        return explode(',', $this->getConfig('allowed_payment_methods'));
    }

    /**
     * Returns true is exclude groups feature is enabled.
     *
     * @return bool
     */
    public function isExcludeGroupsEnabled()
    {
        return $this->getConfigFlag('exclude_groups_enable');
    }

    /**
     * Return default error message
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->getConfig('error_message');
    }

    /**
     * Return if error should be handled
     *
     * @return bool
     */
    public function getHandleError()
    {
        return $this->getConfigFlag('handle_error_code');
    }

    /**
     * Returns if XML should be shown
     *
     * @return bool
     */
    public function getShowXml()
    {
        return $this->getConfigFlag('show_xml');
    }

    /**
     * Return if email for manual check should be send
     *
     * @return bool
     */
    public function getSendEmailForManualCheck()
    {
        return $this->getConfigFlag('send_email_for_manual_check');
    }

    /**
     * Get manual checking email template
     *
     * @return string
     */
    public function getManualCheckingEmailTemplate()
    {
        return $this->getConfig('manual_checking_email_template');
    }

    /**
     * Returns sender email address
     *
     * @return string
     */
    public function getSenderEmail()
    {
        return $this->scopeConfig->getValue('trans_email/ident_general/email', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getSenderName()
    {
        return $this->scopeConfig->getValue('trans_email/ident_general/email', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getRecipientEmail()
    {
        return $this->scopeConfig->getValue('trans_email/ident_sales/email', ScopeInterface::SCOPE_STORE);
    }

    public function getRecipientName()
    {
        return $this->scopeConfig->getValue('trans_email/ident_sales/name', ScopeInterface::SCOPE_STORE);
    }

    /**
     * Return log level
     *
     * @return int
     */
    public function getLogLevel()
    {
        return (int)$this->getConfig('log_level');
    }

    /**
     * Retrieve array of payment methods
     *
     * @return array
     */
    public function getPaymentArray()
    {
        $purchaseTypes = [];
        foreach ($this->scopeConfig->getValue('creditpass') as $key => $arr) {
            if (strpos($key, 'purchase_type_') !== false) {
                if (!empty($arr['payment_method']) && !empty($arr['purchase_type_code'])) {
                    $purchaseTypes[$arr['payment_method']] = $arr['purchase_type_code'];
                }
            }
        }
        return $purchaseTypes;
    }

    /**
     * Get excluded customer groups
     *
     * @return array
     */
    public function getExcludedCustomerGroups()
    {
        return explode(',', $this->getConfig('exclude_groups'));
    }
}