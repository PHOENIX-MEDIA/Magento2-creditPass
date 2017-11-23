<?php

/**
 * creditPass for Magento 2
 *
 * @category    Phoenix
 * @package     Phoenix_Creditpass
 * @copyright   Copyright (c) 2016 PHOENIX MEDIA GmbH (http://www.phoenix-media.eu)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Phoenix\Creditpass;

use Magento\Framework\ObjectManager\ConfigInterface as ObjectManagerConfig;
use Magento\TestFramework\ObjectManager;

class DiConfigConfigurationTest extends \PHPUnit\Framework\TestCase
{
    public function testConfigDataVirtualType()
    {
        $type = Model\Session\Storage::class;
        $expectedType = \Magento\Framework\Session\Storage::class;
        $this->assertVirtualType($expectedType, $type);

        $this->assertDiArgumentSame('creditpass', $type, 'namespace');
    }

    public function testArgumentsExist()
    {
        $type = Model\Session::class;
        $this->assertDiArgumentType(Model\Session\Storage::class, $type, 'storage');

        $type = Log\Handler::class;
        $this->assertDiArgumentType(\Magento\Framework\Filesystem\Driver\File::class, $type, 'filesystem');

        $type = Log\Logger::class;
        $this->assertDiArgumentSame('creditpassLogger', $type, 'name');
        $this->assertDiArgumentItem(Log\Handler::class, $type, 'handlers');

        $type = Controller\Checkout\CheckPayment::class;
        $this->assertDiArgumentType(Log\Logger::class, $type, 'logger');

        $type = Model\Api::class;
        $this->assertDiArgumentType(Log\Logger::class, $type, 'logger');

        $type = Model\Plugin\PaymentMethodApplicable::class;
        $this->assertDiArgumentType(Log\Logger::class, $type, 'logger');

        $type = Model\Plugin\QuotePaymentImportData::class;
        $this->assertDiArgumentType(Log\Logger::class, $type, 'logger');

        $type = Model\RiskCheck::class;
        $this->assertDiArgumentType(\Magento\Checkout\Model\Session::class, $type, 'checkoutSession');
        $this->assertDiArgumentType(Log\Logger::class, $type, 'logger');
    }

    public function testPreferences()
    {
        $type = Api\ApiInterface::class;
        $this->assertDiPreference(Model\Api::class, $type);

        $type = Api\RequestBuilderInterface::class;
        $this->assertDiPreference(Model\RequestBuilder::class, $type);

        $type = Api\Data\SessionInterface::class;
        $this->assertDiPreference(Model\Session::class, $type);
    }

    /*
    * @return ObjectManagerConfig
    */
    private function getDiConfig()
    {
        return ObjectManager::getInstance()->get(ObjectManagerConfig::class);
    }

    /*
     * @param string $expectedType
     * @param string $type
    */
    private function assertVirtualType($expectedType, $type)
    {
        $this->assertSame($expectedType, $this->getDiConfig()->getInstanceType($type));
    }

    /*
     * @param string $expectedValue
     * @param string $type
     * @param string $argumentName
    */
    private function assertDiArgumentSame($expectedValue, $type, $argumentName)
    {
        $arguments = $this->getDiConfig()->getArguments($type);
        if(!isset($arguments[$argumentName]))
        {
            $this->fail(sprintf('No argument "%" configured for %s', $argumentName, $type));
        }
        $this->assertSame($expectedValue, $arguments[$argumentName]);
    }

    /*
     * @param string $expectedType
     * @param string $type
     * @param string $argumentName
    */
    private function assertDiArgumentType($expectedType, $type, $argumentName)
    {
        $arguments = $this->getDiConfig()->getArguments($type);
        if(!isset($arguments[$argumentName]))
        {
            $this->fail(sprintf('No arguments "%s" configured for %s', $argumentName, $type));
        }

        if(!isset($arguments[$argumentName]['instance']))
        {
            $this->fail(sprintf('Argument "%s" for %s not xsi:type="object"', $argumentName, $type));
        }
        $this->assertSame($expectedType, $arguments[$argumentName]['instance']);
    }

    /*
     * @param string $expectedType
     * @param string $type
     * @param string $argumentName
    */
    private function assertDiArgumentItem($expectedType, $type, $argumentName)
    {
        $arguments = $this->getDiConfig()->getArguments($type);
        if(!isset($arguments[$argumentName]))
        {
            $this->fail(sprintf('No arguments "%s" configured for %s', $argumentName, $type));
        }

        if(!isset($arguments[$argumentName]['system']['instance']))
        {
            $this->fail(sprintf('Argument "%s" for %s not xsi:type="object"', $argumentName, $type));
        }
        $this->assertSame($expectedType, $arguments[$argumentName]['system']['instance']);
    }

    /*
     * @param string $expectedValue
     * @param string $type
    */
    private function assertDiPreference($expectedValue, $type)
    {
        $preferenceValue = $this->getDiConfig()->getPreference($type);
        $this->assertSame($expectedValue, $preferenceValue);
    }
    
}
