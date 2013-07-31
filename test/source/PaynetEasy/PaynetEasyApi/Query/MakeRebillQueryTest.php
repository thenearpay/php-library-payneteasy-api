<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Query\Prototype\PaymentQueryTest;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\Customer;
use PaynetEasy\PaynetEasyApi\PaymentData\RecurrentCard;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-06-12 at 16:22:54.
 */
class MakeRebillQueryTest extends PaymentQueryTest
{
    protected $paymentStatus = Payment::STATUS_CAPTURE;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new MakeRebillQuery('_');
    }

    public function testCreateRequestProvider()
    {
        return array(array
        (
            sha1
            (
                self::END_POINT .
                self::CLIENT_ID .
                '99' .                          // amount
                self::RECURRENT_CARD_FROM_ID .
                self::SIGNING_KEY
            )
        ));
    }

    protected function getPayment()
    {
        return new Payment(array
        (
            'client_id'             => self::CLIENT_ID,
            'paynet_id'             => self::PAYNET_ID,
            'description'           => 'This is test payment',
            'amount'                =>  0.99,
            'currency'              => 'USD',
            'customer'              => new Customer(array
            (
                'ip_address'            => '127.0.0.1',
            )),
            'recurrent_card_from'   => new RecurrentCard(array
            (
                'paynet_id'             => self::RECURRENT_CARD_FROM_ID,
                'cvv2'                  => 123
            ))
        ));
    }
}
