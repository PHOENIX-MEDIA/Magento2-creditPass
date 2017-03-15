<?php
/**
 * Created by PhpStorm.
 * User: hong.quang
 * Date: 03.01.2017
 * Time: 15:12
 */

namespace Phoenix\Creditpass\Test\Unit\Observer;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Phoenix\Creditpass\Model\RiskCheck;
use Phoenix\Creditpass\Observer\AddOrderComment;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order;
use Magento\Framework\Event;
use Magento\Framework\Event\ObserverInterface;

class AddOrderCommentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var AddOrderComment
     */
    protected $mockAddOrderComment;

    /**
     * @var Observer
     */
    protected $mockObserver;

    /**
     * @var RiskCheck
     */
    protected $riskCheck;

    /**
     * @var Order
     */
    protected $mockOrder;

    /**
     * * @var Event
     */
    protected $mockEvent;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->riskCheck = $this->getMock(RiskCheck::class, [], [], '', false);

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

        $this->mockAddOrderComment = $this->objectManagerHelper->getObject(
            'Phoenix\Creditpass\Observer\AddOrderComment',
            [
                'riskCheck' => $this->riskCheck
            ]
        );
    }

    public function testImplementsObserverInterface()
    {
        $this->assertInstanceOf(ObserverInterface::class, $this->mockAddOrderComment);
    }

    public function testAddOrderComment()
    {
        $orderId = 123;
        $orderIncrementId = 123456;

        $this->mockOrder->method('getId')
            ->willReturn($orderId);

        $this->mockOrder->method('getIncrementId')
            ->willReturn($orderIncrementId);

        $this->mockEvent->method('getOrder')
            ->willReturn($this->mockOrder);

        $this->mockObserver->method('getEvent')
            ->willReturn($this->mockEvent);

        $this->mockAddOrderComment->execute($this->mockObserver);
    }
}