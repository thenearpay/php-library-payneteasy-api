<?php

namespace PaynetEasy\Paynet\Workflow;

use PaynetEasy\Paynet\Transport\FakeGatewayClient;
use PaynetEasy\Paynet\Transport\Response;
use PaynetEasy\Paynet\OrderData\Order;
use PaynetEasy\Paynet\OrderData\Customer;
use PaynetEasy\Paynet\OrderData\CreditCard;
use PaynetEasy\Paynet\OrderData\RecurrentCardInterface;

/**
 * Test class for Sale.
 * Generated by PHPUnit on 2012-06-14 at 11:50:13.
 */
class SaleTest extends WorkflowTestPrototype
{
    /**
     * Test class
     * @var string
     */
    protected $class            = 'SaleWorkflow';

    public function getTestData()
    {
        $customer               = new Customer
        (
            array
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
            )
        );

        $card                   = new CreditCard
        (
            array
            (
                'card_printed_name'         => 'Vasya Pupkin',
                'credit_card_number'        => '4485 9408 2237 9130',
                'expire_month'              => '12',
                'expire_year'               => '14',
                'cvv2'                      => '084'
            )
        );

        $order                  = new Order
        (
            array
            (
                'client_orderid'            => 'CLIENT-112233',
                'order_desc'                => 'This is test order',
                'amount'                    => 0.99,
                'currency'                  => 'USD',
                'ipaddress'                 => '127.0.0.1',
                'site_url'                  => 'http://example.com'
            )
        );

        return array($customer, $card, $order);
    }

    /**
     * Checking the output parameters
     */
    public function testRequest()
    {
        list($customer, $card, $order) = $this->getTestData();

        $this->order = $order;

        $order->setCustomer($customer);
        $order->setCreditCard($card);

        FakeGatewayClient::$response  = new Response(array
        (
            'type'              => 'async-response',
            'status'            => 'processing',
            'paynet-order-id'   => 'PAYNET-112233',
            'merchant-order-id' => 'CLIENT-112233',
            'serial-number'     => md5(time())
        ));

        $this->query->processOrder($order);

        $request                = array
        (
            'client_orderid'    => 'CLIENT-112233',
            'order_desc'        => 'This is test order',
            'card_printed_name' => 'Vasya Pupkin',
            'first_name'        => 'Vasya',
            'last_name'         => 'Pupkin',
            'birthday'          => '112681',
            'address1'          => '2704 Colonial Drive',
            'city'              => 'Houston',
            'state'             => 'TX',
            'zip_code'          => '1235',
            'country'           => 'US',
            'phone'             => '660-485-6353',
            'cell_phone'        => '660-485-6353',
            'email'             => 'vass.pupkin@example.com',
            'amount'            => 0.99,
            'currency'          => 'USD',
            'credit_card_number' => '4485940822379130',
            'expire_month'      => '12',
            'expire_year'       => '14',
            'cvv2'              => '084',
            'ipaddress'         => '127.0.0.1',
            'site_url'          => 'http://example.com',
            'control'           => sha1
            (
                $this->config['end_point'].
                'CLIENT-112233'.
                '99'.
                'vass.pupkin@example.com'.
                $this->config['control']
            ),
            'redirect_url'      => $this->config['redirect_url'],
            'server_callback_url' => $this->config['server_callback_url']
        );

        foreach($request as $key => $value)
        {
            $this->assertNotEmpty(FakeGatewayClient::$request[$key], 'Request property no exists: ' . $key);
            $this->assertEquals($value, FakeGatewayClient::$request[$key], "$key not equal '$value'");
        }
    }

    public function testProcessProvider()
    {
        $dataset                = array();

        // PROCESSING
        $response               = array
        (
            'type'              => 'async-response',
            'status'            => 'processing',
            'paynet-order-id'   => 'PAYNET-112233',
            'merchant-order-id' => 'CLIENT-112233',
            'serial-number'     => md5(time())
        );

        $assert                 = array
        (
            'state'             => Order::STAGE_CREATED,
            'status'            => Order::STATUS_PROCESSING
        );

        $dataset[]              = array($assert, $response);

        // VALIDATION-ERROR
        $response               = array
        (
            'type'              => 'validation-error',
            'serial-number'     => md5(time()),
            'error-message'     => 'validation-error message',
            'error-code'        => '1000'
        );

        $assert                 = array
        (
            'state'             => Order::STAGE_ENDED,
            'status'            => Order::STATUS_ERROR,
            'error_message'     => $response['error-message'],
            'error_code'        => $response['error-code'],
            'exception'         => true
        );

        $dataset[]              = array($assert, $response);

        // FILTERED
        $response               = array
        (
            'type'              => 'async-response',
            'status'            => 'filtered',
            'paynet-order-id'   => 'PAYNET-112233',
            'merchant-order-id' => 'CLIENT-112233',
            'serial-number'     => md5(time()),
            'error-message'     => 'test filtered message',
            'error-code'        => '8876'
        );

        $assert                 = array
        (
            'state'             => Order::STAGE_ENDED,
            'status'            => Order::STATUS_DECLINED,
            'error_message'     => $response['error-message'],
            'error_code'        => $response['error-code']
        );

        $dataset[]              = array($assert, $response);

        // Type = error
        $response               = array
        (
            'type'              => 'error',
            'error_message'     => 'test type error message',
            'error_code'        => '5'
        );

        $assert                 = array
        (
            'state'             => Order::STAGE_ENDED,
            'status'            => Order::STATUS_ERROR,
            'error_message'     => $response['error_message'],
            'error_code'        => $response['error_code'],
            'exception'         => true
        );

        $dataset[]              = array($assert, $response);

        // ERROR in STATUS
        $response               = array
        (
            'type'              => 'async-response',
            'status'            => 'error',
            'paynet-order-id'   => 'PAYNET-112233',
            'merchant-order-id' => 'CLIENT-112233',
            'serial-number'     => md5(time()),
            'error-message'     => 'test error message',
            'error-code'        => '2'
        );

        $assert                 = array
        (
            'state'             => Order::STAGE_ENDED,
            'status'            => Order::STATUS_ERROR,
            'error_message'     => $response['error-message'],
            'error_code'        => $response['error-code'],
            'exception'         => true
        );

        $dataset[]              = array($assert, $response);

        return $dataset;
    }

    /**
     * @dataProvider testProcessProvider
     */
    public function testProcess($assert, $server_response)
    {
        list($customer, $card, $order) = $this->getTestData();

        $this->order = $order;
        FakeGatewayClient::$response  = new Response($server_response);

        $order->setCustomer($customer);

        if($card instanceof RecurrentCardInterface)
        {
            $order->setRecurrentCardFrom($card);
        }
        else
        {
            $order->setCreditCard($card);
        }

        parent::testProcess($assert, $server_response);
    }

    /**
     * @dataProvider testStatusProvider
     */
    public function testStatus($assert, $server_response)
    {
        list($customer, $card, $order) = $this->getTestData();

        $this->order = $order;
        FakeGatewayClient::$response = new Response(array
        (
            'type'              => 'async-response',
            'status'            => 'processing',
            'paynet-order-id'   => 'PAYNET-112233',
            'merchant-order-id' => 'CLIENT-112233',
            'serial-number'     => md5(time())
        ));

        $order->setCustomer($customer);

        if($card instanceof RecurrentCardInterface)
        {
            $order->setRecurrentCardFrom($card);
        }
        else
        {
            $order->setCreditCard($card);
        }

        $this->query->processOrder($order);

        FakeGatewayClient::$response = new Response($server_response);

        parent::testProcess($assert, $server_response);
    }

    public function testCallbackProvider()
    {
        $dataset                = array();

        // SALE
        $response               = array
        (
            'type'              => 'sale',
            'status'            => 'approved',
            'orderid'           => 'PAYNET-112233',
            'merchant_order'    => 'CLIENT-112233',
            'client_orderid'    => 'CLIENT-112233',
            'amount'            => 0.99,
            'descriptor'        => 'http://descriptor.example.com/',
            // status + orderid + client_orderid + merchant-control
            'control'           => sha1
            (
                'approved'.
                'PAYNET-112233'.
                'CLIENT-112233'.
                self::CONTROL_CODE
            )
        );

        $assert                 = array
        (
            'state'             => Order::STAGE_ENDED,
            'status'            => Order::STATUS_APPROVED
        );

        $dataset[]              = array($assert, $response);

        // PROCESSING
        $response               = array
        (
            'type'              => 'sale',
            'status'            => 'processing',
            'orderid'           => 'PAYNET-112233',
            'merchant_order'    => 'CLIENT-112233',
            'client_orderid'    => 'CLIENT-112233',
            'amount'            => 0.99,
            'descriptor'        => 'http://descriptor.example.com/',
            // status + orderid + client_orderid + merchant-control
            'control'           => sha1
            (
                'processing'.
                'PAYNET-112233'.
                'CLIENT-112233'.
                self::CONTROL_CODE
            )
        );

        $assert                 = array
        (
            'state'             => Order::STAGE_CREATED,
            'status'            => Order::STATUS_PROCESSING
        );

        $dataset[]              = array($assert, $response);

        // DECLINE
        $response               = array
        (
            'type'              => 'sale',
            'status'            => 'declined',
            'orderid'           => 'PAYNET-112233',
            'merchant_order'    => 'CLIENT-112233',
            'client_orderid'    => 'CLIENT-112233',
            'amount'            => 0.99,
            'descriptor'        => 'http://descriptor.example.com/',
            'error_message'     => 'decline message',
            'error_code'        => '1000000',
            'control'           => sha1
            (
                'declined'.
                'PAYNET-112233'.
                'CLIENT-112233'.
                self::CONTROL_CODE
            )
        );

        $assert                 = array
        (
            'state'             => Order::STAGE_ENDED,
            'status'            => Order::STATUS_DECLINED,
            'error_message'     => 'decline message',
            'error_code'        => '1000000'
        );

        $dataset[]              = array($assert, $response);

        // FILTERED
        $response               = array
        (
            'type'              => 'sale',
            'status'            => 'filtered',
            'orderid'           => 'PAYNET-112233',
            'merchant_order'    => 'CLIENT-112233',
            'client_orderid'    => 'CLIENT-112233',
            'amount'            => 0.99,
            'descriptor'        => 'http://descriptor.example.com/',
            'error_message'     => 'filtered message',
            'error_code'        => '1000000',
            'control'           => sha1
            (
                'filtered'.
                'PAYNET-112233'.
                'CLIENT-112233'.
                self::CONTROL_CODE
            )
        );

        $assert                 = array
        (
            'state'             => Order::STAGE_ENDED,
            'status'            => Order::STATUS_DECLINED,
            'error_message'     => 'filtered message',
            'error_code'        => '1000000'
        );

        $dataset[]              = array($assert, $response);

        // Error
        $response               = array
        (
            'type'              => 'sale',
            'status'            => 'error',
            'orderid'           => 'PAYNET-112233',
            'merchant_order'    => 'CLIENT-112233',
            'client_orderid'    => 'CLIENT-112233',
            'amount'            => 0.99,
            'descriptor'        => 'http://descriptor.example.com/',
            'error_message'     => 'error message',
            'error_code'        => '1',
            'control'           => sha1
            (
                'error'.
                'PAYNET-112233'.
                'CLIENT-112233'.
                self::CONTROL_CODE
            )
        );

        $assert                 = array
        (
            'state'             => Order::STAGE_ENDED,
            'status'            => Order::STATUS_ERROR,
            'error_message'     => 'error message',
            'error_code'        => '1',
            'exception'         => true
        );

        $dataset[]              = array($assert, $response);

        return $dataset;
    }

    /**
     * @dataProvider testCallbackProvider
     */
    public function testCallback($assert, $callback)
    {
        list($customer, $card, $order) = $this->getTestData();

        $this->order = $order;
        FakeGatewayClient::$response = new Response(array
        (
            'type'              => 'async-response',
            'status'            => 'processing',
            'paynet-order-id'   => 'PAYNET-112233',
            'merchant-order-id' => 'CLIENT-112233',
            'serial-number'     => md5(time())
        ));

        $order->setCustomer($customer);

        if($card instanceof RecurrentCardInterface)
        {
            $order->setRecurrentCardFrom($card);
        }
        else
        {
            $order->setCreditCard($card);
        }

        $this->query->processOrder($order);

        FakeGatewayClient::$response = new Response(array
        (
            'type'              => 'status-response',
            'status'            => 'processing',
            'html'              => '<HTML>',
            'paynet-order-id'   => 'PAYNET-112233',
            'merchant-order-id' => 'CLIENT-112233',
            'paynet-order-id'   => $order->getPaynetOrderId(),
            'serial-number'     => md5(time())
        ));

        $this->query->processOrder($order);

        parent::testProcess($assert, $callback);
    }
}
