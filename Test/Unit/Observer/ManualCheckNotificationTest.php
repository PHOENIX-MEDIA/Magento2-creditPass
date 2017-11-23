<?php

/**
 * creditPass for Magento 2
 *
 * @category    Phoenix
 * @package     Phoenix_Creditpass
 * @copyright   Copyright (c) 2016 PHOENIX MEDIA GmbH (http://www.phoenix-media.eu)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Phoenix\Creditpass\Test\Unit\Observer;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Phoenix\Creditpass\Model\RiskCheck;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Phoenix\Creditpass\Model\Config;
use Magento\Sales\Model\Order;
use Phoenix\Creditpass\Observer\ManualCheckNotification;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ManualCheckNotificationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var RiskCheck
     */
    protected $riskCheck;


    /** @var Order */
    protected $mockOrder;

    /**
     * @var ManualCheckNotification
     */
    protected $mockManualCheckNotification;

    /**
     * * @var Event
     */
    protected $mockEvent;

    /**
     * @var Observer
     */
    protected $mockObserver;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->config = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->riskCheck = $this->getMockBuilder(RiskCheck::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->transportBuilder = $this->getMockBuilder(TransportBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManager = $this->getMockBuilder(StoreManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockEvent = $this->getMockBuilder('\Magento\Framework\Event')
            ->disableOriginalConstructor()
            ->setMethods(['getStore', 'getResult', 'getQuote', 'getOrder'])
            ->getMock();

        $this->mockOrder = $this->getMockBuilder('\Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getIncrementId', 'addStatusToHistory', 'save'])
            ->getMock();

        $this->mockObserver = $this->getMockBuilder('\Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->setMethods(['getEvent'])
            ->getMock();


        $this->mockManualCheckNotification = $this->objectManagerHelper->getObject(
            'Phoenix\Creditpass\Observer\ManualCheckNotification',
            [
                'riskCheck' => $this->riskCheck,
                'config' => $this->config,
                'transportBuilder' => $this->transportBuilder,
                'storeManager' => $this->storeManager
            ]
        );
    }

    public function testImplementsObserverInterface()
    {
        $this->assertInstanceOf(ObserverInterface::class, $this->mockManualCheckNotification);
    }

    public function testSendManualCheckNotification()
    {
        $storeId = 1;
        $orderId = 123;
        $orderIncrementId = 123456;
        $email_template_path = 'manual_checking_email.html';

        $this->riskCheck->method('getAnswerCode')
            ->willReturn(RiskCheck::ANSWER_CODE_MANUAL_CHECK);

        $this->riskCheck->method('isManualCheckRequired')->willReturn(true);

        $this->mockOrder->method('getId')
            ->willReturn($orderId);

        $this->mockOrder->method('getIncrementId')
            ->willReturn($orderIncrementId);

        $this->mockEvent->method('getOrder')
            ->willReturn($this->mockOrder);

        $this->mockObserver->method('getEvent')
            ->willReturn($this->mockEvent);

        $this->mockOrder->method('addStatusToHistory')
            ->with(\Magento\Sales\Model\Order::STATE_HOLDED)
            ->willReturn('Manual check is needed.');

        $this->mockOrder->method('save')
            ->willReturn($this->mockOrder);

        $this->config->method('getSendEmailForManualCheck')
            ->willReturn(true);

        $this->config->method('getManualCheckingEmailTemplate')
            ->willReturn($email_template_path);

        $this->config->method('getSenderEmail')
            ->willReturn('valid@mail.com');

        $this->config->method('getSenderName')
            ->willReturn('Sender Name');

        $this->config->method('getRecipientEmail')
            ->willReturn('valid@mail.com');

        $this->config->method('getRecipientName')
            ->willReturn('Recipient Name');

        $this->transportBuilder->method('setTemplateIdentifier')
            ->with($email_template_path)
            ->willReturn($this->transportBuilder);

        $this->transportBuilder->method('setTemplateOptions')
            ->willReturn($this->transportBuilder);

        $this->transportBuilder->method('setTemplateVars')
            ->willReturn($this->transportBuilder);

        $this->transportBuilder->method('setFrom')
            ->willReturn($this->transportBuilder);

        $this->transportBuilder->method('addTo')
            ->willReturn($this->transportBuilder);

        $storeInterface = $this->getMockBuilder(\Magento\Store\Api\Data\StoreInterface::class)->disableOriginalConstructor()->getMock();

        $storeInterface->method('getId')
            ->wilLReturn($storeId);

        $this->storeManager->method('getStore')
            ->wilLReturn($storeInterface);

        $transport = $this->getMockBuilder(\Magento\Framework\Mail\TransportInterface::class)->getMock();

        $this->transportBuilder
            ->method('getTransport')
            ->willReturn($transport);

        $transport->expects($this->once())
            ->method('sendMessage')->willReturn($transport);

        $this->mockManualCheckNotification->execute($this->mockObserver);

    }
}
