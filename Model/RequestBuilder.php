<?php
/**
 * creditPass for Magento 2
 *
 * @category    Phoenix
 * @package     Phoenix_Creditpass
 * @copyright   Copyright (c) 2016 PHOENIX MEDIA GmbH (http://www.phoenix-media.eu)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Phoenix\Creditpass\Model;

use Magento\Framework\View\Element\Context;
use Phoenix\Creditpass\Api\RequestBuilderInterface;
use Magento\Quote\Model\Quote;

class RequestBuilder implements RequestBuilderInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var Quote
     */
    protected $quote;

    /**
     * @var string
     */
    protected $paymentMethod;

    /**
     * @var \SimpleXMLElement
     */
    protected $preparedXml;

    /**
     * RequestBuilder constructor.
     *
     * @param Config $config
     * @param Context $context
     */
    public function __construct(Config $config, Context $context)
    {
        $this->config = $config;
        $this->escaper = $context->getEscaper();
    }

    /**
     * @param Quote $quote
     *
     * @return RequestBuilderInterface
     */
    public function setQuote(Quote $quote)
    {
        $this->quote = $quote;
        $this->preparedXml = null;
        return $this;
    }

    /**
     * @param string $paymentMethod
     *
     * @return RequestBuilderInterface
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
        $this->preparedXml = null;
        return $this;
    }

    /**
     * Get request XML
     *
     * return \SimpleXMLElement
     */
    public function getXml()
    {
        if (is_null($this->quote)) {
            throw new \LogicException('Quote object not set.');
        }

        if (is_null($this->preparedXml)) {
            $paymentArray = $this->config->getPaymentArray();
            $address = $this->quote->getBillingAddress();

            $taType = '27920';
            $processingCode = ($this->config->isLiveMode()) ? '1' : '8';

            $xml = simplexml_load_string("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<REQUEST></REQUEST>");
            $customerobj = $xml->addChild('CUSTOMER');
            $customerobj->addChild('AUTH_ID', $this->config->getApiAuthId());
            $customerobj->addChild('AUTH_PW', $this->config->getApiAuthPassword());
            $customerobj->addChild('CUSTOMER_TA_ID', md5(time()));
            $processobj = $xml->addChild('PROCESS');
            $processobj->addChild('TA_TYPE', $taType);
            $processobj->addChild('PROCESSING_CODE', $processingCode);
            $processobj->addChild('REQUESTREASON', 'ABK');
            $queryobj = $xml->addChild('QUERY');
            $queryobj->addChild('FIRST_NAME', $this->escaper->escapeHtml(substr($address->getFirstname(), 0, 64)));
            $queryobj->addChild('LAST_NAME', $this->escaper->escapeHtml(substr($address->getLastname(), 0, 64)));
            $customerDob = '';
            if ($dob = $this->quote->getCustomerDob()) {
                $customerDob = date('Y-m-d', strtotime($dob));
            }
            $queryobj->addChild('DOB', $customerDob);
            $queryobj->addChild('ADDR_STREET_FULL', $this->escaper->escapeHtml(substr($address->getStreetFull(), 0, 50)));
            $queryobj->addChild('ADDR_ZIP', substr($address->getPostcode(), 0, 5));
            $queryobj->addChild('ADDR_CITY', $this->escaper->escapeHtml(substr($address->getCity(), 0, 32)));
            $queryobj->addChild('ADDR_COUNTRY', $address->getCountryId());
            $queryobj->addChild('CUSTOMERIP', ($this->quote->getRemoteIp() ? $this->quote->getRemoteIp() : '127.0.0.1'));
            $queryobj->addChild('CUSTOMEREMAIL', $this->escaper->escapeHtml(substr($address->getEmail(), 0, 128)));
            $queryobj->addChild('CUSTOMERTEL', $this->escaper->escapeHtml(substr($address->getTelephone(), 0, 64)));
            $queryobj->addChild('COMPANY_NAME', $this->escaper->escapeHtml(substr($address->getCompany(), 0, 64)));
            $queryobj->addChild('AMOUNT', round($this->quote->getGrandTotal() * 100));

            // add purchase type with appended group type
            $purchaseType = isset($paymentArray[$this->paymentMethod]) ? $paymentArray[$this->paymentMethod] : '';
            $purchaseType .= ($this->quote->getCheckoutMethod() != Quote::CHECKOUT_METHOD_LOGIN_IN) ? '1' : '2';
            $queryobj->addChild('PURCHASE_TYPE', $purchaseType);

            $this->preparedXml = $xml;
        }

        return $this->preparedXml;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getXml()->asXML();
    }
}