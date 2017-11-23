<?php
/**
 * creditPass for Magento 2
 *
 * @category    Phoenix
 * @package     Phoenix_Creditpass
 * @copyright   Copyright (c) 2016 PHOENIX MEDIA GmbH (http://www.phoenix-media.eu)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Phoenix\Creditpass\Api;


interface ApiInterface
{
    /**
     * @param RequestBuilderInterface $request
     *
     * @return \SimpleXMLElement
     */
    public function sendRequest(RequestBuilderInterface $request);
}