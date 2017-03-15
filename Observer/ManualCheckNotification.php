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
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;
use Phoenix\Creditpass\Model\Config;
use Phoenix\Creditpass\Model\RiskCheck;

/**
 * Class ManualCheckNotification
 *
 * @package Phoenix\Creditpass\Observer
 */
class ManualCheckNotification implements ObserverInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var RiskCheck
     */
    protected $riskCheck;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @param Config $config
     * @param RiskCheck $riskCheck
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Config $config,
        RiskCheck $riskCheck,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->config = $config;
        $this->riskCheck = $riskCheck;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Sends email if manual order checking is required.
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->riskCheck->isManualCheckRequired()) {
            $order = $observer->getEvent()->getOrder();
            /* @var Order $order */

            $order->addStatusToHistory(Order::STATE_HOLDED, __('Manual check is needed.'));
            $this->orderRepository->save($order);

            if ($this->config->getSendEmailForManualCheck()) {
                $transport = $this->transportBuilder->setTemplateIdentifier($this->config->getManualCheckingEmailTemplate())
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->storeManager->getStore()->getId(),
                        ]
                    )
                    ->setTemplateVars(['entity_increment_id' => $order->getIncrementId()])
                    ->setFrom(['email' => $this->config->getSenderEmail(), 'name' => $this->config->getSenderName()])
                    ->addTo($this->config->getRecipientEmail(), $this->config->getRecipientName())
                    ->getTransport();

                $transport->sendMessage();
            }
        }
    }
}