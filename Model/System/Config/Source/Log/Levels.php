<?php
/**
 * creditPass for Magento 2
 *
 * @category    Phoenix
 * @package     Phoenix_Creditpass
 * @copyright   Copyright (c) 2016 PHOENIX MEDIA GmbH (http://www.phoenix-media.eu)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


namespace Phoenix\Creditpass\Model\System\Config\Source\Log;

use Psr\Log\LoggerInterface;

class Levels implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Levels constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->logger->getLevels() as $label => $value) {
            $options[] = ['value' => $value, 'label' => __($label)];
        }
        return $options;
    }
}