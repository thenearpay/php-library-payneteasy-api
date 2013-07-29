<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\Exception\ValidationException;

class AbstractPaymentQuery extends AbstractQuery
{
    /**
     * Status for payment, when it is processing by this query
     *
     * @var string
     */
    static protected $paymentStatus;

    /**
     * {@inheritdoc}
     */
    static protected $responseFieldsDefinition = array
    (
        'type',
        'status',
        'paynet-order-id',
        'merchant-order-id',
        'serial-number'
    );

    /**
     * {@inheritdoc}
     */
    static protected $successResponseType = 'async-response';

    public function createRequest(PaymentTransaction $paymentTransaction)
    {
        $paymentTransaction->getPayment()->setStatus(static::$paymentStatus);

        $request = parent::createRequest($paymentTransaction);

        $paymentTransaction->setStatus(PaymentTransaction::STATUS_PROCESSING);

        return $request;
    }

    /**
     * {@inheritdoc}
     */
    protected function validatePaymentTransaction(PaymentTransaction $paymentTransaction)
    {
        if ($paymentTransaction->getPayment()->hasProcessingTransaction())
        {
            throw new ValidationException('Payment can not has processing payment transaction');
        }

        parent::validatePaymentTransaction($paymentTransaction);
    }

    /**
     * {@inheritdoc}
     */
    protected function validateQueryDefinition()
    {
        parent::validateQueryDefinition();

        if (empty(static::$paymentStatus))
        {
            throw new RuntimeException('You must configure paymentStatus property');
        }
    }
}