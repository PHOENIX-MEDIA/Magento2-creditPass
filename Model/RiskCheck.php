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


use Magento\Framework\Session\SessionManager as CheckoutSession;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Phoenix\Creditpass\Api\ApiInterface;
use Phoenix\Creditpass\Api\RequestBuilderInterface;
use Phoenix\Creditpass\Api\Data\SessionInterface;
use Psr\Log\LoggerInterface;

class RiskCheck
{
    /**
     * Authorized
     */
    const ANSWER_CODE_AUTHORIZED = 0;

    /**
     * Not authorized
     */
    const ANSWER_CODE_NOT_AUTHORIZED = 1;

    /**
     * Error
     */
    const ANSWER_CODE_ERROR = -1;

    /**
     * Manual check required
     */
    const ANSWER_CODE_MANUAL_CHECK = 2;

    /**
     * Check result passed
     */
    const CHECK_RESULT_PASSED = 'PASSED';

    /**
     * Check result failed
     */
    const CHECK_RESULT_FAILED = 'FAILED';

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var TimezoneInterface
     */
    protected $dateTime;

    /**
     * @var ApiInterface
     */
    protected $api;

    /**
     * @var RequestBuilderInterface
     */
    protected $requestBuilder;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * RiskCheck constructor.
     *
     * @param Config $config
     * @param SessionInterface $session
     * @param CheckoutSession $checkoutSession
     * @param TimezoneInterface $dateTime
     * @param ApiInterface $api
     * @param RequestBuilderInterface $requestBuilder
     * @param OrderRepositoryInterface $orderRepository
     * @param LoggerInterface $logger
     */
    public function __construct (
        Config $config,
        SessionInterface $session,
        CheckoutSession $checkoutSession,
        TimezoneInterface $dateTime,
        ApiInterface $api,
        RequestBuilderInterface $requestBuilder,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->session = $session;
        $this->checkoutSession = $checkoutSession;
        $this->dateTime = $dateTime;
        $this->api = $api;
        $this->requestBuilder = $requestBuilder;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
    }

    /**
     * Create a request and send it to creditPass service. Directly call processResponse() to handle the response.
     *
     * @param string $paymentMethod
     * @return bool
     */
    public function performRiskCheck($paymentMethod)
    {
        if (!$this->isEnabled()) {
            $this->logger->debug('Module is deactivated');
            return true;
        }

        $this->logger->debug('Performing risk check for payment method '.$paymentMethod);

        $quote = $this->checkoutSession->getQuote();
        /* @var \Magento\Quote\Model\Quote $quote */
        $guest = ($quote->getCheckoutMethod() != $quote::CHECKOUT_METHOD_LOGIN_IN) ? true : false;
        $this->session->setTimestamp($this->dateTime->date()->format('M d Y, H:i:s'));
        $this->session->setQuoteId($quote->getId());

        $customerGroupId = $quote->getCustomer()->getGroupId();
        if (!$guest && $this->isCustomerGroupExcluded($customerGroupId)) {
            $this->logger->debug('Skip check because of customer group (group ID.'.$customerGroupId.'; quote ID:'.$quote->getId().')');
            $this->session->setAnswerCode(RiskCheck::ANSWER_CODE_AUTHORIZED);
            $this->session->setAnswerText(sprintf('%s', __('Customer has not been checked because he is member of a trusted group.')));
            $this->session->setCheckResult(RiskCheck::CHECK_RESULT_PASSED);
            return true;
        }

        // only check critical payment methods
        if ($paymentMethod && in_array($paymentMethod, $this->config->getAllowedPaymentMethods())) {
            $this->logger->debug('Skip check because payment method is always allowed (payment method: '.$paymentMethod.'; quote ID: '.$quote->getId().')');
            return true;
        }

        // only check rating once
        if ($this->isFilterActive()) {
            $this->logger->debug('Skipping check because customer has been declined before.');
            return false;
        }

        try {
            $this->logger->debug('Calling API for risk check');
            $this->requestBuilder->setQuote($quote)->setPaymentMethod($paymentMethod);
            $responseXml = $this->api->sendRequest($this->requestBuilder);
            return $this->processResponse($responseXml);
        } catch (\Exception $e) {
            if (!$this->config->getHandleError()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return the if group id is in the list of exclude groups
     *
     * @param int $group
     *
     * @return bool
     */
    protected function isCustomerGroupExcluded($group)
    {
        if ($this->config->isExcludeGroupsEnabled()) {
            return in_array($group, $this->config->getExcludedCustomerGroups(), true);
        }
        return false;
    }

    /**
     * Add risk check result as order comment
     *
     * @param Order $order
     *
     * @return void
     */
    public function addOrderComment(Order $order)
    {
        // if creditPass check has been performed and comments exists for current order/quote
        if ($this->isEnabled() && $this->session->getAnswerText() != '' &&
            $this->session->getQuoteId() == $order->getQuoteId())
        {
            $this->logger->debug('Saving creditPass risk check result for order: ' . $order->getIncrementId());

            // prepare comment
            $comment = ['creditPass (' . $this->session->getTimestamp() . ')'];
            switch (intval($this->session->getAnswerCode())) {
                case RiskCheck::ANSWER_CODE_ERROR:
                    $comment[] = __('An error occurred during check.');
                    break;
                case RiskCheck::ANSWER_CODE_AUTHORIZED:
                    $comment[] = __('Authorized');
                    break;
                case RiskCheck::ANSWER_CODE_NOT_AUTHORIZED:
                    $comment[] = __('Not authorized');
                    break;
                case RiskCheck::ANSWER_CODE_MANUAL_CHECK:
                    $comment[] = __('Manual check is needed.');
                    break;
            }
            $comment[] = '';
            $comment[] = __('creditPass answer text:') . ' ' . $this->session->getAnswerText();
            $comment[] = '';
            $comment[] = __('creditPass answer details:') . ' ' . $this->session->getAnswerDetails();
            if ($this->config->getShowXml()) {
                $comment[] = '';
                $comment[] = __('XML request:') . ' ' . $this->session->getRequestRaw();
                $comment[] = '';
                $comment[] = __('XML response:') . ' ' . $this->session->getResponseRaw();
            }

            // save comment
            $order->addStatusHistoryComment(implode("<br/>\n", $comment));

            $this->orderRepository->save($order);
        }
    }

    /**
     * Process the response and save it to the session.
     *
     * @param \SimpleXMLElement $responseXml
     *
     * @return bool
     */
    protected function processResponse(\SimpleXMLElement $responseXml)
    {
        $answerCode = intval(sprintf('%s', $responseXml->PROCESS->ANSWER_CODE));
        $this->session->setAnswerCode($answerCode);
        $this->session->setAnswerText(sprintf('%s', $responseXml->PROCESS->ANSWER_TEXT));
        $this->session->setAnswerDetails(sprintf('%s', $responseXml->PROCESS->ANSWER_DETAILS));
        $this->session->setRequestRaw((string)$this->requestBuilder);
        $this->session->setResponseRaw($responseXml->asXML());

        if ($answerCode === RiskCheck::ANSWER_CODE_AUTHORIZED || $answerCode === RiskCheck::ANSWER_CODE_MANUAL_CHECK ||
            ($answerCode === RiskCheck::ANSWER_CODE_ERROR && $this->config->getHandleError())
        ) {
            $this->session->setCheckResult(RiskCheck::CHECK_RESULT_PASSED);
            $this->logger->info('Answer code: ' . $answerCode. '; result: ' . RiskCheck::CHECK_RESULT_PASSED);
            return true;
        } else {
            $this->session->setCheckResult(RiskCheck::CHECK_RESULT_FAILED);
            $this->logger->info('Answer code: ' . $answerCode. '; result: ' . RiskCheck::CHECK_RESULT_FAILED);
            return false;
        }
    }

    /**
     * Checks if creditPass check is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->config->isEnabled();
    }

    /**
     * Check if filtering was enabled
     *
     * @return boolean
     */
    public function isFilterActive()
    {
        if ($this->session->getCheckResult() === RiskCheck::CHECK_RESULT_FAILED) {
            return true;
        }
        return false;
    }

    /**
     * Returns true if manual check is required
     *
     * @return bool
     */
    public function isManualCheckRequired()
    {
        if ($this->session->getAnswerCode() === RiskCheck::ANSWER_CODE_MANUAL_CHECK) {
            return true;
        }
        return false;
    }

    /**
     * Clear all session values
     */
    public function resetRiskCheckResult()
    {
        $this->session->clearStorage();
    }
}