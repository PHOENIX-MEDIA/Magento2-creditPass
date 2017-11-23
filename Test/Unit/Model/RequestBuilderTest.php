<?php

/**
 * creditPass for Magento 2
 *
 * @category    Phoenix
 * @package     Phoenix_Creditpass
 * @copyright   Copyright (c) 2016 PHOENIX MEDIA GmbH (http://www.phoenix-media.eu)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Phoenix\Creditpass\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Quote\Model\Quote;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Phoenix\Creditpass\Api\RequestBuilderInterface;
use Phoenix\Creditpass\Model\Config;
use Phoenix\Creditpass\Model\RequestBuilder;
use Magento\Framework\View\Element\Context;
use Magento\Framework\Escaper;
use Magento\Framework\Model\Context as ModelContext;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Payment;

class RequestBuilderTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfigMock;

    /**
     * @var Quote
     */
    protected $mockQuote;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var RequestBuilder
     */
    protected $requestBuilder;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var ModelContext
     */
    protected $modelContext;

    /**
     * @var Address
     */
    protected $mockAddress;

    /**
     * @var Payment
     */
    protected $mockPayment;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class)
                                    ->disableOriginalConstructor()->getMock();

        $this->modelContext = $this->getMockBuilder(Context::class)
                                ->disableOriginalConstructor()->getMock();

        $this->escaper = $this->getMockBuilder(\Magento\Framework\Escaper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->context = $this->objectManagerHelper->getObject(
            'Magento\Framework\View\Element\Context',
            [
                'escaper' => $this->escaper
            ]
        );

        $this->config = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestBuilder = $this->objectManagerHelper->getObject(
            'Phoenix\Creditpass\Model\RequestBuilder',
            [
                'config' => $this->config,
                'context' => $this->context
            ]
        );

        $this->mockQuote = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockAddress = $this->getMockBuilder(Address::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockPayment = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testImplementRequestBuilderInterface()
    {
        $this->assertInstanceOf(RequestBuilderInterface::class, $this->requestBuilder);
    }

    public function testGetXml()
    {
        $this->setQuote();
        $this->requestBuilder->setQuote($this->mockQuote);
        $result = [
            'banktransfer' => '2',
            'cashondelivery' => '2'
        ];

        $this->config->method('getPaymentArray')
            ->willReturn($result);

        $this->config->method('isLiveMode')
            ->willReturn(true);

        $auth_id = 'T920434';
        $this->config->method('getApiAuthId')
            ->willReturn($auth_id);

        $auth_pw = 'password';
        $this->config->method('getApiAuthPassword')
            ->willReturn($auth_pw);
        
        $paymentMethod = 'banktransfer';
        $this->requestBuilder->setPaymentMethod($paymentMethod);

        $xml_request = simplexml_load_string($this->requestBuilder->getXml()->asXML());
        // assert Equals format XML
        $this->assertEquals(true, isset($xml_request->CUSTOMER->CUSTOMER_TA_ID));
        $this->assertEquals(true, isset($xml_request->PROCESS->TA_TYPE));
        $this->assertEquals(true, isset($xml_request->PROCESS->PROCESSING_CODE));
        $this->assertEquals(true, isset($xml_request->PROCESS->REQUESTREASON));
        $this->assertEquals(true, isset($xml_request->QUERY->FIRST_NAME));
        $this->assertEquals(true, isset($xml_request->QUERY->LAST_NAME));
        $this->assertEquals(true, isset($xml_request->QUERY->DOB));
        $this->assertEquals(true, isset($xml_request->QUERY->ADDR_STREET_FULL));
        $this->assertEquals(true, isset($xml_request->QUERY->ADDR_ZIP));
        $this->assertEquals(true, isset($xml_request->QUERY->ADDR_CITY));
        $this->assertEquals(true, isset($xml_request->QUERY->ADDR_COUNTRY));
        $this->assertEquals(true, isset($xml_request->QUERY->CUSTOMERIP));
        $this->assertEquals(true, isset($xml_request->QUERY->CUSTOMEREMAIL));
        $this->assertEquals(true, isset($xml_request->QUERY->CUSTOMERTEL));
        $this->assertEquals(true, isset($xml_request->QUERY->COMPANY_NAME));
        $this->assertEquals(true, isset($xml_request->QUERY->AMOUNT));
        $this->assertEquals(true, isset($xml_request->QUERY->PURCHASE_TYPE));
    }

    private function setQuote()
    {
        $this->mockQuote->method('getId')
            ->willReturn('101');

        $this->mockQuote->method('getRemoteIp')
            ->willReturn('1.1.1.1');

        $this->mockQuote->method('getGrandTotal')
            ->willReturn(100.00);

        $this->mockQuote->method('getCheckoutMethod')
            ->willReturn(Quote::CHECKOUT_METHOD_LOGIN_IN);

        $this->mockPayment->method('getMethod')
            ->willReturn('banktransfer');

        $this->mockQuote->method('getPayment')
            ->willReturn($this->mockPayment);

        $this->mockAddress->method('getId')
            ->willReturn('100');

        $this->mockAddress->method('getFirstname')
            ->willReturn('Max');

        $this->mockAddress->method('getLastname')
            ->willReturn('Mustermann');

        $this->mockAddress->method('getStreetFull')
            ->willReturn('Berlin str.');

        $this->mockAddress->method('getCity')
            ->willReturn('Berlin');

        $this->mockAddress->method('getEmail')
            ->willReturn('phoenix@email.com');

        $this->mockAddress->method('getTelephone')
            ->willReturn('123456789');

        $this->mockAddress->method('getCompany')
            ->willReturn('Phoenix Media');

        $this->mockQuote->method('getBillingAddress')
            ->willReturn($this->mockAddress);
    }

}
