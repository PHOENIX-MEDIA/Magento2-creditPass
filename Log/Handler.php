<?php
/**
 * creditPass for Magento 2
 *
 * @category    Phoenix
 * @package     Phoenix_Creditpass
 * @copyright   Copyright (c) 2016 PHOENIX MEDIA GmbH (http://www.phoenix-media.eu)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Phoenix\Creditpass\Log;

use Magento\Framework\Filesystem\DriverInterface;
use Phoenix\Creditpass\Model\Config;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/creditpass.log';

    /**
     * Handler constructor.
     *
     * @param DriverInterface $filesystem
     * @param null|string     $filePath
     */
    public function __construct(DriverInterface $filesystem, $filePath = null)
    {
        $manager = \Magento\Framework\App\ObjectManager::getInstance();
        $config = $manager->get('Phoenix\Creditpass\Model\Config');
        /* @var Config $config */
        $logLevel = $config->getLogLevel();
        if ($logLevel) {
            $this->loggerType = $logLevel;
        }

        parent::__construct($filesystem, $filePath);
    }
}