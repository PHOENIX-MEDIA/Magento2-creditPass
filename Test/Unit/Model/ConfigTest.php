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
use Phoenix\Creditpass\Model\Config;

class ConfigTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfigMock;

    /** @var Config */
    protected $config;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->config = $this->objectManagerHelper->getObject(
            'Phoenix\Creditpass\Model\Config',
            [
                'scopeConfig' => $this->scopeConfigMock
            ]
        );
    }

    public function testIsEnabled()
    {
        $enabled = true;
        $this->scopeConfigMock->method('isSetFlag')
            ->willReturn($enabled);
        $this->assertEquals($enabled, $this->config->isEnabled());
    }

    public function testGetApiUrl()
    {
        $cp_url = 'https://secure.creditpass.de/atgw/authorize.cfm';
        $this->scopeConfigMock->method('getValue')
            ->willReturn($cp_url);
        $this->assertEquals($cp_url, $this->config->getApiUrl());
    }

    public function testGetApiAuthId()
    {
        $auth_id = 'xxx';
        $this->scopeConfigMock->method('getValue')
            ->willReturn($auth_id);
        $this->assertEquals($auth_id, $this->config->getApiAuthId());
    }

    public function testGetApiAuthPassword()
    {
        $auth_pw = 'password';
        $this->scopeConfigMock->method('getValue')
            ->willReturn($auth_pw);
        $this->assertEquals($auth_pw, $this->config->getApiAuthPassword());
    }

    public function testIsLiveMode()
    {
        $live_mode = true;
        $this->scopeConfigMock->method('isSetFlag')
            ->willReturn($live_mode);
        $this->assertEquals($live_mode, $this->config->isLiveMode());
    }

    public function testIsExcludeGroupsEnabled()
    {
        $exclude_groups_enable = true;
        $this->scopeConfigMock->method('isSetFlag')
            ->willReturn($exclude_groups_enable);
        $this->assertEquals($exclude_groups_enable, $this->config->isExcludeGroupsEnabled());
    }

    public function testGetErrorMessage()
    {
        $error_message = 'Test error Message';
        $this->scopeConfigMock->method('getValue')
            ->willReturn($error_message);
        $this->assertEquals($error_message, $this->config->getErrorMessage());
    }

    public function testGetHandleError()
    {
        $handle_error_code = true;
        $this->scopeConfigMock->method('isSetFlag')->willReturn($handle_error_code);
        $this->assertEquals($handle_error_code, $this->config->getHandleError());
    }

    public function testGetShowXml()
    {
        $show_xml = true;
        $this->scopeConfigMock->method('isSetFlag')->willReturn($show_xml);
        $this->assertEquals($show_xml, $this->config->getShowXml());
    }

    public function testGetSenderEmail()
    {
        $sender = "sender@abc.com";
        $this->scopeConfigMock->method('getValue')->willReturn($sender);
        $this->assertEquals($sender, $this->config->getSenderEmail());
    }

    public function testGetSenderName()
    {
        $sender_name = "Mustermann";
        $this->scopeConfigMock->method('getValue')->willReturn($sender_name);
        $this->assertEquals($sender_name, $this->config->getSenderName());
    }

    public function testGetRecipientEmail()
    {
        $email = "recipient@abc.com";
        $this->scopeConfigMock->method('getValue')->willReturn($email);
        $this->assertEquals($email, $this->config->getRecipientEmail());
    }

    public function testGetRecipientName()
    {
        $name = "Musterman";
        $this->scopeConfigMock->method('getValue')->willReturn($name);
        $this->assertEquals($name, $this->config->getRecipientName());
    }

    public function testGetAllowedPaymentMethods()
    {
        $allowed_payment_methods = 'banktransfer, cashondelivery, checkorder';
        $this->scopeConfigMock->method('getValue')->willReturn($allowed_payment_methods);
        $this->assertEquals(explode(',',  $allowed_payment_methods), $this->config->getAllowedPaymentMethods());
    }

    public function testGetPaymentArray()
    {
        $array = ['settings' => ['active' => '1', 'live_mode' => '0', 'handle_error_code' => '1', 'auth_id' => 'xxx', 'auth_pw' => 'yyy'],
            'purchase_type_1' => ['payment_method' => 'banktransfer', 'purchase_type_code' => '2'],
            'purchase_type_2' => ['payment_method' => 'cashondelivery', 'purchase_type_code' => '2'],
        ];
        $result = [
            'banktransfer' => '2',
            'cashondelivery' => '2'
        ];
        $this->scopeConfigMock->method('getValue')->willReturn($array);
        $this->assertEquals($result, $this->config->getPaymentArray());
    }
}