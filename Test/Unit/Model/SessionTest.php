<?php

/**
 * creditPass for Magento 2
 *
 * @category    Phoenix
 * @package     Phoenix_Creditpass
 * @copyright   Copyright (c) 2016 PHOENIX MEDIA GmbH (http://www.phoenix-media.eu)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Phoenix\Creditpass\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Phoenix\Creditpass\Api\Data\SessionInterface;
use Phoenix\Creditpass\Model\Session;

class SessionTest extends \PHPUnit\Framework\TestCase
{

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var Session */
    protected $session;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->session = $this->getMockBuilder(Session::class)
            ->setMethods(['getData', 'setData'])->disableOriginalConstructor()->getMock();
    }

    public function testImplementSessionInterface()
    {
        $this->assertInstanceOf(SessionInterface::class, $this->session);
    }

    public function testGetAnswerCode()
    {
        $value = 'test answer code';
        $this->session->method('getData')->with(SessionInterface::ANSWER_CODE)->willReturn($value);
        $this->assertEquals($value, $this->session->getAnswerCode(SessionInterface::ANSWER_CODE));
    }

    public function testSetAnswerCode()
    {
        $code = 12345;
        $this->session->method('setData')
            ->with(SessionInterface::ANSWER_CODE, $code);

        $this->session->setAnswerCode($code);
    }

    public function testGetAnswerDetails()
    {
        $value = 'test answer details';
        $this->session->method('getData')
            ->with(SessionInterface::ANSWER_DETAILS)->willReturn($value);

        $this->assertEquals($value, $this->session
                                         ->getAnswerDetails(SessionInterface::ANSWER_DETAILS));
    }

    public function testSetAnswerDetails()
    {
        $detail = 'test answer details';
        $this->session->method('setData')
            ->with(SessionInterface::ANSWER_DETAILS, $detail);

        $this->session->setAnswerDetails($detail);
    }

    public function testGetAnswerText()
    {
        $value = 'test answer text';
        $this->session->method('getData')
            ->with(SessionInterface::ANSWER_TEXT)->willReturn($value);

        $this->assertEquals($value, $this->session
            ->getAnswerText(SessionInterface::ANSWER_TEXT));
    }

    public function testSetAnswerText()
    {
        $text = 'test answer text';
        $this->session->method('setData')
            ->with(SessionInterface::ANSWER_TEXT, $text);

        $this->session->setAnswerText($text);
    }

    public function testGetQuoteId()
    {
        $quoteId = 12345;
        $this->session->method('getData')
            ->with(SessionInterface::QUOTE_ID)->willReturn($quoteId);

        $this->assertEquals($quoteId, $this->session
            ->getQuoteId(SessionInterface::QUOTE_ID));
    }

    public function testSetQuoteId()
    {
        $quoteId = 12345;
        $this->session->method('setData')
            ->with(SessionInterface::QUOTE_ID, $quoteId);

        $this->session->setQuoteId($quoteId);
    }

    public function testGetRequestRaw()
    {
        $request = 'Test Request Raw';

        $this->session->method('getData')
            ->with(SessionInterface::REQUEST_RAW)->willReturn($request);

        $this->assertEquals($request, $this->session
            ->getRequestRaw(SessionInterface::REQUEST_RAW));
    }

    public function testSetRequestRaw()
    {
        $request = 'Test Request Raw';

        $this->session->method('setData')
            ->with(SessionInterface::REQUEST_RAW, $request);

        $this->session->setRequestRaw($request);
    }

    public function testGetResponseRaw()
    {
        $response = 'Test Response Raw';

        $this->session->method('getData')
            ->with(SessionInterface::RESPONSE_RAW)->willReturn($response);

        $this->assertEquals($response, $this->session
            ->getResponseRaw(SessionInterface::RESPONSE_RAW));
    }

    public function testSetResponseRaw()
    {
        $response = 'Test Response Raw';

        $this->session->method('setData')
            ->with(SessionInterface::RESPONSE_RAW, $response);

        $this->session->setResponseRaw($response);
    }

    public function testGetCheckResult()
    {
        $code = 0;
        $this->session->method('getData')
            ->with(SessionInterface::RESULT_CODE)->willReturn($code);

        $this->assertEquals($code, $this->session
            ->getCheckResult(SessionInterface::RESULT_CODE));
    }

    public function testSetCheckResult()
    {
        $code = 0;
        $this->session->method('setData')
            ->with(SessionInterface::RESULT_CODE, $code);

        $this->session->setCheckResult($code);
    }

    public function testGetTimestamp()
    {
        $time = 'DEC 12 2016';
        $this->session->method('getData')
            ->with(SessionInterface::TIMESTAMP)->willReturn($time);

        $this->assertEquals($time, $this->session
            ->getTimestamp(SessionInterface::TIMESTAMP));
    }

    public function testSetTimestamp()
    {
        $time = 'DEC 12 2016';
        $this->session->method('setData')
            ->with(SessionInterface::TIMESTAMP, $time);

        $this->session->setTimestamp($time);
    }
}