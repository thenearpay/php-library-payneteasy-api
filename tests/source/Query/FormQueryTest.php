<?php

namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\Transport\Response;
use PaynetEasy\Paynet\OrderData\Customer;
use PaynetEasy\Paynet\OrderData\Order;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-06-18 at 18:17:58.
 */
class FormQueryTest extends QueryTestPrototype
{
    /**
     * @var FormQuery
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new FormQuery($this->getConfig());
        $this->object->setApiMethod('sale-form');
    }

    public function testCreateRequestProvider()
    {
        return array(array
        (
            sha1
            (
                self::END_POINT .
                self::CLIENT_ORDER_ID .
                9910 .
               'vass.pupkin@example.com' .
                self::SIGN_KEY
            )
        ));
    }

    public function testProcessResponseDeclinedProvider()
    {
        return array(array(array
        (
            'type'              => 'async-form-response',
            'status'            => 'filtered',
            'merchant-order-id' =>  self::CLIENT_ORDER_ID,
            'serial-number'     =>  md5(time()),
            'error-message'     => 'test filtered message',
            'error-code'        =>  8876
        )));
    }

    /**
     * @dataProvider testProcessResponseProcessingProvider
     */
    public function testProcessResponseProcessing(array $response)
    {
        $order = $this->getOrder();

        $this->object->processResponse($order, new Response($response));

        $this->assertOrderStates($order, Order::STAGE_REDIRECTED, Order::STATUS_PROCESSING);
        $this->assertFalse($order->hasErrors());
    }

    public function testProcessResponseProcessingProvider()
    {
        return array(array(array
        (
            'type'              => 'async-form-response',
            'status'            => 'processing',
            'merchant-order-id' =>  self::CLIENT_ORDER_ID,
            'paynet-order-id'   =>  self::PAYNET_ORDER_ID,
            'serial-number'     =>  md5(time()),
            'redirect-url'      => 'http://redirect-url.com'
        )));
    }

    public function testProcessResponseErrorProvider()
    {
        return array(array(
        // Payment error after check
        array
        (
            'type'              => 'async-form-response',
            'status'            => 'error',
            'merchant-order-id' =>  self::CLIENT_ORDER_ID,
            'serial-number'     =>  md5(time()),
            'error-message'     => 'status error message',
            'error-code'        =>  2
        ),
        // Validation error
        array
        (
            'type'              => 'validation-error',
            'error-message'     => 'validation error message',
            'error-code'        =>  1
        ),
        // Immediate payment error
        array
        (
            'type'              => 'error',
            'error-message'     => 'immediate error message',
            'error-code'        =>  1
        )));
    }

    protected function getOrder()
    {
        // создание клиента и платежа с использованием массивов
        $customer = new Customer(array
        (
            'first_name'    => 'Vasya',
            'last_name'     => 'Pupkin',
            'email'         => 'vass.pupkin@example.com',
            'address'       => '2704 Colonial Drive',
            'birthday'      => '112681',
            'city'          => 'Houston',
            'state'         => 'TX',
            'zip_code'      => '1235',
            'country'       => 'US',
            'phone'         => '660-485-6353',
            'cell_phone'    => '660-485-6353'
        ));

        $order = new Order(array
        (
            'client_orderid'            =>  self::CLIENT_ORDER_ID,
            'order_desc'                => 'This is test order',
            'amount'                    =>  99.1,
            'currency'                  => 'USD',
            'ipaddress'                 => '127.0.0.1',
            'site_url'                  => 'http://example.com'
        ));

        $order->setCustomer($customer);

        return $order;
    }
}
