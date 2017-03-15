<?php
/**
 * creditPass for Magento 2
 *
 * @category    Phoenix
 * @package     Phoenix_Creditpass
 * @copyright   Copyright (c) 2016 PHOENIX MEDIA GmbH (http://www.phoenix-media.eu)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Phoenix\Creditpass\Model\Plugin;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Payment;
use Phoenix\Creditpass\Model\Config;
use Phoenix\Creditpass\Model\RiskCheck;

class QuotePaymentImportData
{
    /**
     * @var RiskCheck
     */
    protected $riskCheck;

    /**
     * @var Config
     */
    protected $config;

    /**
     * PaymentMethodApplicable constructor.
     *
     * @param Config $config
     * @param RiskCheck $riskCheck
     */
    public function __construct(
        Config $config,
        RiskCheck $riskCheck
    ) {
        $this->config = $config;
        $this->riskCheck = $riskCheck;
    }

    /**
     * @param Payment $subject
     * @param array   $data
     *
     * @throws LocalizedException
     */
    public function beforeImportData(Payment $subject, Array $data)
    {
        if (!empty($data['method']) && $this->riskCheck->performRiskCheck($data['method']) === false) {
            throw new LocalizedException(
                __($this->config->getErrorMessage())
            );
        }
    }
}