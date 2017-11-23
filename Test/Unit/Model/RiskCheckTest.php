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
use Phoenix\Creditpass\Model\RiskCheck;
use Phoenix\Creditpass\Model\Config;
use Phoenix\Creditpass\Api\Data\SessionInterface as SessionInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Phoenix\Creditpass\Api\ApiInterface;
use Phoenix\Creditpass\Model\RequestBuilder;
use Magento\Quote\Model\Quote\Payment;
use Psr\Log\LoggerInterface;
use Magento\Customer\Model\Customer;
use Magento\Sales\Api\OrderRepositoryInterface;

class RiskCheckTest extends \PHPUnit\Framework\TestCase
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
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var TimezoneInterface
     */
    protected $dateTime;

    /**
     * @var ApiInterface
     */
    protected $api;

    /** @var RequestBuilder */
    protected $requestBuilder;

    /**
     * @var RiskCheck
     */
    protected $mockRiskCheck;

    /**
     * @var Quote
     */
    protected $mockQuote;

    /**
     * @var Payment
     */
    protected $mockPayment;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->config = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->session = $this->getMockBuilder(SessionInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->checkoutSession = $this->getMockBuilder(CheckoutSession::class)
            ->disableOriginalConstructor()->getMock();

        $this->dateTime = $this->getMockBuilder(TimezoneInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->api = $this->getMockBuilder(ApiInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->requestBuilder = $this->getMockBuilder(RequestBuilder::class)
            ->disableOriginalConstructor()->getMock();

        $this->mockQuote = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockPayment = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->customer = $this->getMockBuilder(Customer::class)
            ->disableOriginalConstructor()->getMock();

        $this->orderRepository = $this->getMockBuilder(OrderRepositoryInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->mockRiskCheck = $this->getMockBuilder(RiskCheck::class)->setConstructorArgs([
            'config' => $this->config,
            'session' => $this->session,
            'checkoutSession' => $this->checkoutSession,
            'dateTime' => $this->dateTime,
            'api' => $this->api,
            'requestBuilder' => $this->requestBuilder,
            'orderRepository' => $this->orderRepository,
            'logger' => $this->logger
        ])
            ->enableArgumentCloning()
            ->enableProxyingToOriginalMethods()
            ->getMock();
    }

    public function testPerformRiskCheck()
    {
        $quoteId = 12345;
        $this->setQuote($quoteId);
        $this->setConfig();

        $today = new \DateTime();
        $this->dateTime->method('date')
            ->willReturn($today);

        $this->session->method('setQuoteId')
            ->willReturn($quoteId);

        $responseXml = '<RESPONSE><PROCESS><ANSWER_CODE>0</ANSWER_CODE><ANSWER_TEXT>OK</ANSWER_TEXT></PROCESS></RESPONSE>';

        $this->api->method('sendRequest')
            ->willReturn(simplexml_load_string($responseXml));

        $this->mockRiskCheck->method('processResponse')->willReturn(true);

        $paymentMethod = 'banktransfer';

        $result = $this->mockRiskCheck->performRiskCheck($paymentMethod);

        $this->assertEquals(true, $result);
    }

    public function testAddOrderComment() {
        $quoteId = 12345;
        $incrementId = 45455;
        $this->setQuote($quoteId);

        $this->setConfig();
        $order = $this->getMockBuilder(\Magento\Sales\Model\Order::class)
            ->disableOriginalConstructor()
            ->getMock();

        $order->method('getQuoteId')->willReturn($quoteId);
        $order->method('getIncrementId')->willReturn($incrementId);

        $this->session->method('getAnswerText')
            ->willReturn('Creditpass order comment');

        $this->session->method('getQuoteId')
            ->willReturn($quoteId);

        $this->session->method('getTimestamp')
            ->willReturn('Dec 12, 2016');

        $this->session->method('getAnswerCode')
            ->willReturn(RiskCheck::ANSWER_CODE_AUTHORIZED);

        $this->session->method('getAnswerDetails')
            ->willReturn('Creditpass trusfull payment method');

        $this->config->method('getShowXml')->willReturn(false);
    }

    private function setQuote($quoteId)
    {
        $this->mockQuote->method('getId')
            ->willReturn($quoteId);

        $this->mockQuote->method('getCheckoutMethod')
            ->willReturn(Quote::CHECKOUT_METHOD_LOGIN_IN);

        $this->mockPayment->method('getMethod')
            ->willReturn('banktransfer');

        $this->mockQuote->method('getPayment')
            ->willReturn($this->mockPayment);

        $this->checkoutSession->method('getQuote')
            ->willReturn($this->mockQuote);

        $this->customer->method('getGroupId')
            ->willReturn("cg not in");

        $this->mockQuote->method('getCustomer')
            ->willReturn($this->customer);

        $this->requestBuilder->method('setQuote')
            ->willReturn($this->mockQuote);

        $xml_request = '<?xml version="1.0" encoding="UTF-8"?>
                        <REQUEST>
                            <CUSTOMER>
                                <AUTH_ID>T920434</AUTH_ID>
                                <AUTH_PW>pfR!n3syfE2ykA</AUTH_PW>
                                <CUSTOMER_TA_ID>dafc16db2c5350e73c5e7f7b637b55b6</CUSTOMER_TA_ID>
                            </CUSTOMER>
                            <PROCESS>
                                <TA_TYPE>27920</TA_TYPE>
                                <PROCESSING_CODE>8</PROCESSING_CODE>
                                <REQUESTREASON>ABK</REQUESTREASON>
                            </PROCESS>
                            <QUERY>
                                <FIRST_NAME>Max</FIRST_NAME>
                                <LAST_NAME>Mustermann</LAST_NAME>
                                <DOB/>
                                <ADDR_STREET_FULL>Berlin str.</ADDR_STREET_FULL>
                                <ADDR_ZIP>13347</ADDR_ZIP>
                                <ADDR_CITY>Berlin</ADDR_CITY>
                                <ADDR_COUNTRY>DE</ADDR_COUNTRY>
                                <CUSTOMERIP>1.1.1.1</CUSTOMERIP>
                                <CUSTOMEREMAIL>test@gmail.com</CUSTOMEREMAIL>
                                <CUSTOMERTEL>015227788548</CUSTOMERTEL>
                                <COMPANY_NAME>Test GmbH</COMPANY_NAME>
                                 <AMOUNT>85600</AMOUNT>
                                 <PURCHASE_TYPE>22</PURCHASE_TYPE>
                            </QUERY>
                        </REQUEST>';

        $this->requestBuilder->method('__toString')
            ->willReturn($xml_request);
    }

    private function setConfig()
    {
        $exclude_groups_arr = '1, 2, 3';
        $arr_payment = [
            'banktransfer' => '2',
            'cashondelivery' => '2'
        ];
        $allowed_payment_methods = ['cashondelivery, checkorder'];

        $this->config->method('isEnabled')
            ->willReturn(true);

        $this->config->method('exclude_groups')
            ->willReturn($exclude_groups_arr);

        $this->config->method('isCustomerGroupExcluded')
            ->willReturn(false);

        $this->config->method('isEnabled')
            ->willReturn(true);

        $this->config->method('getPaymentArray')
            ->willReturn($arr_payment);

        $this->config->method('getAllowedPaymentMethods')
            ->willReturn($allowed_payment_methods);
    }

}
