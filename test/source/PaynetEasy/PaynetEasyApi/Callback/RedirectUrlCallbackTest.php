<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-06-19 at 13:09:32.
 */
class RedirectUrlCallbackTest extends CallbackTestPrototype
{
    /**
     * @var RedirectUrlCallback
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new RedirectUrlCallback($this->getConfig());
    }

    public function testProcessCallbackApprovedProvider()
    {
        return array(array(array
        (
            'status'            => 'approved',
            'amount'            =>  0.99,
            'orderid'           =>  self::PAYNET_PAYMENT_ID,
            'merchant_order'    =>  self::CLIENT_PAYMENT_ID,
            'client_orderid'    =>  self::CLIENT_PAYMENT_ID,
        )));
    }

    public function testProcessCallbackDeclinedProvider()
    {
        return array(
        array(array
        (
            'status'            => 'declined',
            'amount'            =>  0.99,
            'orderid'           =>  self::PAYNET_PAYMENT_ID,
            'merchant_order'    =>  self::CLIENT_PAYMENT_ID,
            'client_orderid'    =>  self::CLIENT_PAYMENT_ID,
        )),
        array(array
        (
            'status'            => 'filtered',
            'amount'            =>  0.99,
            'orderid'           =>  self::PAYNET_PAYMENT_ID,
            'merchant_order'    =>  self::CLIENT_PAYMENT_ID,
            'client_orderid'    =>  self::CLIENT_PAYMENT_ID,
        )));
    }

    public function testProcessCallbackErrorProvider()
    {
        return array(array(array
        (
            'status'            => 'error',
            'amount'            =>  0.99,
            'orderid'           =>  self::PAYNET_PAYMENT_ID,
            'merchant_order'    =>  self::CLIENT_PAYMENT_ID,
            'client_orderid'    =>  self::CLIENT_PAYMENT_ID,
            'error_message'     => 'test type error message',
            'error_code'        =>  5
        )));
    }
}
