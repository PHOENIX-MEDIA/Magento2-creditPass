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

use Magento\Quote\Model\Quote;

interface RequestBuilderInterface
{
    /**
     * @param Quote $quote
     *
     * @return RequestBuilderInterface
     */
    public function setQuote(Quote $quote);

    /**
     * @param string $paymentMethod
     *
     * @return RequestBuilderInterface
     */
    public function setPaymentMethod($paymentMethod);

    /**
     * Get request XML
     *
     * @return string
     */
    public function getXml();
}