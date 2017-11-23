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

use Magento\Payment\Model\Checks\SpecificationInterface;
use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Model\Quote;
use Phoenix\Creditpass\Model\Config;
use Phoenix\Creditpass\Model\RiskCheck;
use Psr\Log\LoggerInterface;

class PaymentMethodApplicable
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

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
     * @param LoggerInterface $logger
     * @param Config $config
     * @param RiskCheck $riskCheck
     */
    public function __construct(
        LoggerInterface $logger,
        Config $config,
        RiskCheck $riskCheck
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->riskCheck = $riskCheck;
    }

    /**
     * Filter invalid payment methods.
     *
     * @param SpecificationInterface $subject
     * @param \Closure $proceed
     * @param MethodInterface $paymentMethod
     * @param Quote $quote
     *
     * @return bool
     */
    public function aroundIsApplicable(SpecificationInterface $subject, \Closure $proceed, MethodInterface $paymentMethod, Quote $quote)
    {
        $result = $proceed($paymentMethod, $quote);

        if ($result && $this->riskCheck->isEnabled() && $this->riskCheck->isFilterActive()) {
            // only allow enabled methods
            $allowedMethods = $this->config->getAllowedPaymentMethods();
            if (is_array($allowedMethods) && !in_array($paymentMethod->getCode(), $allowedMethods)) {
                $this->logger->debug('Payment filtering active, removed '.$paymentMethod->getCode());
                return false;
            }
        } elseif ($result) {
            $this->logger->debug(sprintf(
                'Payment filtering NOT active for method "%s". moduleActive: %s;isFilterActive: %s',
                $paymentMethod->getCode(), (int)$this->riskCheck->isEnabled(), (int)$this->riskCheck->isFilterActive()
            ));
        }
        return $result;
    }
}
