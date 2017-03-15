<?php
/**
 * creditPass for Magento 2
 *
 * @category    Phoenix
 * @package     Phoenix_Creditpass
 * @copyright   Copyright (c) 2016 PHOENIX MEDIA GmbH (http://www.phoenix-media.eu)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Phoenix\Creditpass\Api\Data;


interface SessionInterface
{
    /**
     * Timestamp
     */
    const TIMESTAMP = 'timestamp';

    /**
     * Quote ID
     */
    const QUOTE_ID = 'quote_id';

    /**
     * Answer code
     */
    const ANSWER_CODE = 'answer_code';

    /**
     * Answer details
     */
    const ANSWER_DETAILS = 'answer_details';

    /**
     * Answer text
     */
    const ANSWER_TEXT = 'answer_text';

    /**
     * Result
     */
    const RESULT_CODE = 'result_code';

    /**
     * Raw request
     */
    const REQUEST_RAW = 'request_raw';

    /**
     * Raw response
     */
    const RESPONSE_RAW = 'response_raw';

    /**
     * Get answer code
     *
     * @return string|null
     */
    public function getAnswerCode();

    /**
     * Set answer code
     *
     * @param int $code
     *
     * @return $this
     */
    public function setAnswerCode($code);

    /**
     * Get answer details
     *
     * @return string|null
     */
    public function getAnswerDetails();

    /**
     * Set answer details
     *
     * @param string $details
     *
     * @return $this
     */
    public function setAnswerDetails($details);

    /**
     * Get answer text
     *
     * @return string|null
     */
    public function getAnswerText();

    /**
     * Set answer text
     *
     * @param string $text
     *
     * @return $this
     */
    public function setAnswerText($text);

    /**
     * Get quote ID
     *
     * @return int|null
     */
    public function getQuoteId();

    /**
     * Set quote ID
     *
     * @param int $quoteId
     *
     * @return $this
     */
    public function setQuoteId($quoteId);

    /**
     * Get raw request
     *
     * @return string|null
     */
    public function getRequestRaw();

    /**
     * Set raw request
     *
     * @param string $xml
     *
     * @return $this
     */
    public function setRequestRaw($xml);

    /**
     * Get raw response
     *
     * @return string|null
     */
    public function getResponseRaw();

    /**
     * Set raw response
     *
     * @param string $xml
     *
     * @return $this
     */
    public function setResponseRaw($xml);

    /**
     * Get check result
     *
     * @return string|null
     */
    public function getCheckResult();

    /**
     * Set check result
     *
     * @param string $result
     *
     * @return $this
     */
    public function setCheckResult($result);

    /**
     * Get timestamp
     *
     * @return string|null
     */
    public function getTimestamp();

    /**
     * Set timestamp
     *
     * @param string $timestamp
     *
     * @return $this
     */
    public function setTimestamp($timestamp);

    /**
     * Unset all session data
     *
     * @return $this
     */
    public function clearStorage();
}