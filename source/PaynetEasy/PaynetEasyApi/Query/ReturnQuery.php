<?php
namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Query\Prototype\PaymentQuery;
use PaynetEasy\PaynetEasyApi\Utils\Validator;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;

/**
 * @see http://wiki.payneteasy.com/index.php/PnE:Return_Transactions
 */
class ReturnQuery extends PaymentQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('client_orderid',     'payment.clientId',                 true,   Validator::ID),
        array('orderid',            'payment.paynetId',                 true,   Validator::ID),
        array('amount',             'payment.amount',                   true,   Validator::AMOUNT),
        array('currency',           'payment.currency',                 true,   Validator::CURRENCY),
        array('comment',            'payment.comment',                  true,   Validator::MEDIUM_STRING),
        array('login',              'queryConfig.login',                true,   Validator::MEDIUM_STRING)
    );

    /**
     * {@inheritdoc}
     */
    static protected $signatureDefinition = array
    (
        'queryConfig.login',
        'payment.clientId',
        'payment.paynetId',
        'payment.amountInCents',
        'payment.currency',
        'queryConfig.signingKey'
    );

    /**
     * {@inheritdoc}
     */
    static protected $paymentStatus = Payment::STATUS_RETURN;
}