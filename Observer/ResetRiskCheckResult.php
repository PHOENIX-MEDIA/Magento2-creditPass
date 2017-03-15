<?php
/**
 * creditPass for Magento 2
 *
 * @category    Phoenix
 * @package     Phoenix_Creditpass
 * @copyright   Copyright (c) 2016 PHOENIX MEDIA GmbH (http://www.phoenix-media.eu)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Phoenix\Creditpass\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Phoenix\Creditpass\Model\RiskCheck;

class ResetRiskCheckResult implements ObserverInterface
{
    /**
     * @var RiskCheck
     */
    protected $riskCheck;

    /**
     * @param RiskCheck $riskCheck
     */
    public function __construct(RiskCheck $riskCheck)
    {
        $this->riskCheck = $riskCheck;
    }

    /**
     * Sends email if manual order checking is required.
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $this->riskCheck->resetRiskCheckResult();
    }
}