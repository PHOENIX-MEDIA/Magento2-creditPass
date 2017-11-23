<?php
/**
 * Created by PhpStorm.
 * User: hong.quang
 * Date: 04.01.2017
 * Time: 17:03
 */

namespace Phoenix\Creditpass;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Module\ModuleList;
use Magento\TestFramework\ObjectManager;

class CreditpassModuleConfigTest extends \PHPUnit\Framework\TestCase
{

    private $moduleName = 'Phoenix_Creditpass';

    public function testTheModuleIsRegistered()
    {
        $registrar = new ComponentRegistrar();
        $this->assertArrayHasKey($this->moduleName, $registrar->getPaths(ComponentRegistrar::MODULE));
    }

    public function testTheModuleEnable()
    {
        /* @var ObjectManager $objectManager*/
        $objectManager = ObjectManager::getInstance();

        $moduleList = $objectManager->create(ModuleList::class);

        $this->assertTrue($moduleList->has($this->moduleName));
    }

    public function testTheModuleEnableRealEnvironment()
    {
        /* @var ObjectManager $objectManager*/
        $objectManager = ObjectManager::getInstance();

        $dirList = $objectManager->create(DirectoryList::class, ['root' => BP]);
        $configReader = $objectManager->create(DeploymentConfig\Reader::class, ['dirList' => $dirList]);
        $deploymentConfig = $objectManager->create(DeploymentConfig::class, ['reader' => $configReader]);

        $moduleList = $objectManager->create(ModuleList::class, ['config' => $deploymentConfig]);
        $this->assertTrue($moduleList->has($this->moduleName));
    }
}
