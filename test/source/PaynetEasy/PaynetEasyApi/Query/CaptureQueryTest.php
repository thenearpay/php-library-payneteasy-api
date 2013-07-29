<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-06-18 at 16:12:35.
 */
class CaptureQueryTest extends PaymentQueryTestPrototype
{
    /**
     * @var CaptureQuery
     */
    protected $object;

    protected $paymentStatus = Payment::STATUS_CAPTURE;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new CaptureQuery('_');
    }

    public function testCreateRequestProvider()
    {
        return array(array
        (
            sha1
            (
                self::LOGIN .
                self::CLIENT_PAYMENT_ID .
                self::PAYNET_PAYMENT_ID .
                 9910 .
                'EUR' .
                self::SIGNING_KEY
            )
        ));
    }

    protected function getPayment()
    {
        return new Payment(array
        (
            'client_payment_id'     => self::CLIENT_PAYMENT_ID,
            'paynet_payment_id'     => self::PAYNET_PAYMENT_ID,
            'amount'                => 99.1,
            'currency'              => 'EUR',
        ));
    }
}
