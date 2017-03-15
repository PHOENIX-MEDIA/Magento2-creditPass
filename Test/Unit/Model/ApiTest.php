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
use Magento\Framework\App\Config\ScopeConfigInterface;
use Phoenix\Creditpass\Model\Api;
use Phoenix\Creditpass\Model\Config;
use Psr\Log\LoggerInterface;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\HTTP\ZendClient;
use Phoenix\Creditpass\Api\RequestBuilderInterface;

class ApiTest extends \PHPUnit_Framework_TestCase
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
     * @var Api
     */
    protected $api;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ZendClientFactory
     */
    protected $httpClientFactory;

    /**
     * @var ZendClient
     */
    protected $zendClient;

    /**
     * @var \Zend_Http_Client
     */
    protected $zend_http_client;

    /**
     * @var RequestBuilderInterface
     */
    protected $requestBuilder;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->logger = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->disableOriginalConstructor()->getMock();

        $this->httpClientFactory = $this->getMock(
            'Magento\Framework\HTTP\ZendClientFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->zendClient = $this->getMock('\Magento\Framework\HTTP\ZendClient');
        $this->zend_http_client = $this->getMock('\Zend_Http_Client');

        $this->requestBuilder = $this->getMockBuilder(\Phoenix\Creditpass\Api\RequestBuilderInterface::class)
                                    ->disableOriginalConstructor()->getMock();

        $this->scopeConfigMock = $this->getMockBuilder(\Magento\Framework\App\Config\ScopeConfigInterface::class)
                                    ->disableOriginalConstructor()->getMock();

        $this->config = $this->objectManagerHelper->getObject(
            'Phoenix\Creditpass\Model\Config',
            [
                'scopeConfig' => $this->scopeConfigMock
            ]
        );

        $this->api = $this->objectManagerHelper->getObject(
            'Phoenix\Creditpass\Model\Api',
            [
                'config' => $this->config,
                'logger' => $this->logger,
                'httpClientFactory' => $this->httpClientFactory
            ]
        );

    }

    public function testImplementApiInterface()
    {
        $this->assertInstanceOf(\Phoenix\Creditpass\Api\ApiInterface::class, $this->api);
    }

    public function testSendRequest()
    {
        $this->httpClientFactory->expects($this->once())
            ->method('create')->willReturn($this->zendClient);

        $this->zendClient->expects($this->once())->method('setUri')
            ->willReturn($this->zend_http_client);

        $time = ['timeout' => 30];
        $this->zend_http_client->expects($this->once())->method('setConfig')
            ->with($time)->willReturn($this->zend_http_client);

        $cp_url = 'https://secure.creditpass.de/atgw/authorize.cfm';
        $this->scopeConfigMock->method('getValue')->willReturn($cp_url);
        $this->assertEquals($cp_url, $this->config->getApiUrl());

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

        $answer_code_array = ['0', '2', '-1'];
        $xml = $this->api->sendRequest($this->requestBuilder);
        $answer_code = (string)$xml->PROCESS->ANSWER_CODE;
        $this->assertContains($answer_code, $answer_code_array);
        $this->assertNotNull((string)$xml->PROCESS->ANSWER_TEXT);
    }
}