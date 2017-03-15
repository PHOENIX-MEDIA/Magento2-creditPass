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

use Phoenix\Creditpass\Api\Data\SessionInterface;

/**
 * Creditpass session model
 */
class Session extends \Magento\Framework\Session\SessionManager implements SessionInterface
{
    /**
     * @inheritdoc
     */
    public function getAnswerCode()
    {
        return $this->getData(SessionInterface::ANSWER_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setAnswerCode($code)
    {
        return $this->setData(SessionInterface::ANSWER_CODE, $code);
    }

    /**
     * @inheritdoc
     */
    public function getAnswerDetails()
    {
        return $this->getData(SessionInterface::ANSWER_DETAILS);
    }

    /**
     * @inheritdoc
     */
    public function setAnswerDetails($details)
    {
        return $this->setData(SessionInterface::ANSWER_DETAILS, $details);
    }

    /**
     * @inheritdoc
     */
    public function getAnswerText()
    {
        return $this->getData(SessionInterface::ANSWER_TEXT);
    }

    /**
     * @inheritdoc
     */
    public function setAnswerText($text)
    {
        return $this->setData(SessionInterface::ANSWER_TEXT, $text);
    }

    /**
     * @inheritdoc
     */
    public function getQuoteId()
    {
        return $this->getData(SessionInterface::QUOTE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setQuoteId($quoteId)
    {
        return $this->setData(SessionInterface::QUOTE_ID, $quoteId);
    }

    /**
     * @inheritdoc
     */
    public function getRequestRaw()
    {
        return $this->getData(SessionInterface::REQUEST_RAW);
    }

    /**
     * @inheritdoc
     */
    public function setRequestRaw($xml)
    {
        return $this->setData(SessionInterface::REQUEST_RAW, $xml);
    }

    /**
     * @inheritdoc
     */
    public function getResponseRaw()
    {
        return $this->getData(SessionInterface::RESPONSE_RAW);
    }

    /**
     * @inheritdoc
     */
    public function setResponseRaw($xml)
    {
        return $this->setData(SessionInterface::RESPONSE_RAW, $xml);
    }

    /**
     * @inheritdoc
     */
    public function getCheckResult()
    {
        return $this->getData(SessionInterface::RESULT_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setCheckResult($result)
    {
        return $this->setData(SessionInterface::RESULT_CODE, $result);
    }

    /**
     * @inheritdoc
     */
    public function getTimestamp()
    {
        return $this->getData(SessionInterface::TIMESTAMP);
    }

    /**
     * @inheritdoc
     */
    public function setTimestamp($timestamp)
    {
        return $this->setData(SessionInterface::TIMESTAMP, $timestamp);
    }
}
