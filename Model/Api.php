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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\ZendClientFactory;
use Phoenix\Creditpass\Api\ApiInterface;
use Phoenix\Creditpass\Api\RequestBuilderInterface;
use Psr\Log\LoggerInterface;

class Api implements ApiInterface
{
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
     * Api constructor.
     *
     * @param Config $config
     * @param ZendClientFactory $httpClientFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config $config,
        ZendClientFactory $httpClientFactory,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->httpClientFactory = $httpClientFactory;
    }

    /**
     * @param RequestBuilderInterface $requestBuilder
     *
     * @return \SimpleXMLElement
     * @throws LocalizedException
     */
    public function sendRequest(RequestBuilderInterface $requestBuilder)
    {
        try {
            $client = $this->httpClientFactory->create();
            /* @var \Magento\Framework\HTTP\ZendClient $client */
            $client->setUri($this->config->getApiUrl())
                ->setConfig(['timeout' => 30])
                ->setParameterPost('reqxml', (string)$requestBuilder)
                ->setMethod(\Zend_Http_Client::POST);

            $this->logger->info('Request: ' . (string)$requestBuilder);

            /* @var \Zend_Http_Response $response */
            $response = $client->request();
            $responseBody = $response->getBody();

            $this->logger->info('Response: ' . $response->getStatus() . ':' .  $responseBody);

            if ($response->getStatus() != '200') {
                throw new LocalizedException(__('creditPass API failure. Return status %s.', $response->getStatus()));
            }
            if (empty($responseBody)) {
                throw new LocalizedException(__('creditPass API failure. Response can\'t be processed.'));
            }
        } catch (\Exception $e) {
            $responseBody = $this->returnErrorXml($e->getMessage());
        }

        // process response
        $responseXml = @simplexml_load_string($responseBody);
        if ($responseXml === false) {
            throw new LocalizedException(__('creditPass API failure. Response parsing error.'));
        }

        return $responseXml;
    }

    /**
     * Return error -1 XML
     *
     * @param string $error
     *
     * @return string
     */
    protected function returnErrorXml($error)
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
            <RESPONSE>
                <PROCESS>
                    <ANSWER_CODE>-1</ANSWER_CODE>
                    <ANSWER_TEXT>' . $error . '</ANSWER_TEXT>
                </PROCESS>
            </RESPONSE>';
    }
}