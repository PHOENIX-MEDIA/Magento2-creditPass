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

use Magento\TestFramework\Interception\PluginList;
use Magento\TestFramework\ObjectManager;

class CreditpassModulePluginTest extends \PHPUnit_Framework_TestCase
{

    public function testPlugins()
    {
        $type = \Magento\Payment\Model\Checks\Composite::class;
        $expected = Model\Plugin\PaymentMethodApplicable::class;
        $this->assertDiPlugin($expected, 'creditpass', $type);

        $type = \Magento\Quote\Model\Quote\Payment::class;
        $expected = Model\Plugin\QuotePaymentImportData::class;
        $this->assertDiPlugin($expected, 'creditpass', $type);
    }

    private function assertDiPlugin($expected, $name, $type)
    {
        $objectManager = ObjectManager::getInstance();

        $pluginList = $objectManager->create(PluginList::class, []);

        $pluginInfo = $pluginList->get($type, []);

        $this->assertSame($expected, $pluginInfo[$name]['instance']);

    }

}