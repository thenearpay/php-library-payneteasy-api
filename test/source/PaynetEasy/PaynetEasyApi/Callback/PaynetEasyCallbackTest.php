<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-06-19 at 13:22:10.
 */
class ServerCallbackUrlCallbackTest extends CallbackTestPrototype
{
    /**
     * @var ServerCallbackUrlCallback
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new PaynetEasyCallback('_');
    }

    /**
     * @dataProvider testProcessCallbackApprovedProvider
     */
    public function testProcessCallbackApproved(array $callback)
    {
        list($paymentTransaction, $callbackResponse) = parent::testProcessCallbackApproved($callback);

        $this->assertEquals(PaymentTransaction::PROCESSOR_CALLBACK, $paymentTransaction->getProcessorType());
        $this->assertEquals($callbackResponse->getType(), $paymentTransaction->getProcessorName());

        return array($paymentTransaction, $callbackResponse);
    }

    public function testProcessCallbackApprovedProvider()
    {
        return array(array(array
        (
            'type'              => 'sale',
            'status'            => 'approved',
            'amount'            =>  0.99,
            'orderid'           =>  self::PAYNET_ID,
            'merchant_order'    =>  self::CLIENT_ID,
            'client_orderid'    =>  self::CLIENT_ID,
        )));
    }

    public function testProcessCallbackDeclinedProvider()
    {
        return array(
        array(array
        (
            'type'              => 'sale',
            'status'            => 'declined',
            'amount'            =>  0.99,
            'orderid'           =>  self::PAYNET_ID,
            'merchant_order'    =>  self::CLIENT_ID,
            'client_orderid'    =>  self::CLIENT_ID,
        )),
        array(array
        (
            'type'              => 'sale',
            'status'            => 'filtered',
            'amount'            =>  0.99,
            'orderid'           =>  self::PAYNET_ID,
            'merchant_order'    =>  self::CLIENT_ID,
            'client_orderid'    =>  self::CLIENT_ID,
        )));
    }

    public function testProcessCallbackErrorProvider()
    {
        return array(array(array
        (
            'type'              => 'sale',
            'status'            => 'error',
            'amount'            =>  0.99,
            'orderid'           =>  self::PAYNET_ID,
            'merchant_order'    =>  self::CLIENT_ID,
            'client_orderid'    =>  self::CLIENT_ID,
            'error_message'     => 'test type error message',
            'error_code'        =>  5
        )));
    }
}
