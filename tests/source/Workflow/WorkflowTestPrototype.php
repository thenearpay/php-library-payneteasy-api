<?php
namespace PaynetEasy\Paynet\Workflow;

use PHPUnit_Framework_TestCase;
use PaynetEasy\Paynet\Transport\FakeGatewayClient;
use PaynetEasy\Paynet\Query\QueryFactory;
use PaynetEasy\Paynet\Callback\CallbackFactory;
use PaynetEasy\Paynet\OrderData\Order;
use PaynetEasy\Paynet\Exception\PaynetException;

/**
 * Test class for Query.
 * Generated by PHPUnit on 2012-06-14 at 20:08:20.
 */
abstract class WorkflowTestPrototype extends PHPUnit_Framework_TestCase
{
    /**
     * Control code for SIGN
     */
    const CONTROL_CODE  = 'D5F82EC1-8575-4482-AD89-97X6X0X20X22';

    /**
     * Test class
     * @var string
     */
    protected $class;

    /**
     * @var Status
     */
    protected $query;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var \PaynetEasy\Paynet\Transport\FakeGatewayClient
     */
    protected $transport;

    /**
     * @var array
     */
    protected $config;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->transport = new FakeGatewayClient();

        $this->class     = __NAMESPACE__.'\\'.$this->class;


        $this->config    = array
        (
            'login'             => 'test-login',
            'end_point'         => '789',
            'control'           => self::CONTROL_CODE,
            'redirect_url'      => 'https://example.com/redirect_url',
            'server_callback_url' => 'https://example.com/callback_url'
        );

        $this->query     = new $this->class($this->transport,
                                            new QueryFactory,
                                            new CallbackFactory,
                                            $this->config);
    }

    public function testStatusProvider()
    {
        $dataset                = array();

        // APPROVE
        $response               = array
        (
            'type'              => 'status-response',
            'status'            => 'approved',
            'paynet-order-id'   => 'PAYNET-112233',
            'serial-number'     =>  md5(time())
        );

        $assert                 = array
        (
            'state'             => Order::STATE_END,
            'status'            => Order::STATUS_APPROVED
        );

        $dataset[]              = array($assert, $response);

        // DECLINE
        $response               = array
        (
            'type'              => 'status-response',
            'status'            => 'declined',
            'paynet-order-id'   => 'PAYNET-112233',
            'serial-number'     =>  md5(time()),
            'error-message'     => 'test error message',
            'error-code'        => '578'
        );

        $assert                 = array
        (
            'state'             => Order::STATE_END,
            'status'            => Order::STATUS_DECLINED,
            'error_message'     => 'test error message',
            'error_code'        => '578'
        );

        $dataset[]              = array($assert, $response);

        // FILTERED
        $response               = array
        (
            'type'              => 'status-response',
            'status'            => 'filtered',
            'paynet-order-id'   => 'PAYNET-112233',
            'serial-number'     =>  md5(time()),
            'error-message'     => 'test filtered message',
            'error-code'        => '8876'
        );

        $assert                 = array
        (
            'state'             => Order::STATE_END,
            'status'            => Order::STATUS_DECLINED,
            'error_message'     => 'test filtered message',
            'error_code'        => '8876'
        );

        $dataset[]              = array($assert, $response);

        // PROCESSING
        $response               = array
        (
            'type'              => 'status-response',
            'status'            => 'processing',
            'paynet-order-id'   => 'PAYNET-112233',
            'serial-number'     => md5(time())
        );

        $assert                 = array
        (
            'state'             => Order::STATE_PROCESSING,
            'status'            => Order::STATUS_PROCESSING
        );

        $dataset[]              = array($assert, $response);

        // 3D redirect
        $response               = array
        (
            'type'              => 'status-response',
            'status'            => 'processing',
            'html'              => '<HTML>',
            'paynet-order-id'   => 'PAYNET-112233',
            'serial-number'     => md5(time())
        );

        $assert                 = array
        (
            'state'             => Order::STATE_REDIRECT,
            'status'            => null
        );

        $dataset[]              = array($assert, $response);

        // URL redirect
        $response               = array
        (
            'type'              => 'status-response',
            'status'            => 'processing',
            'redirect-url'      => 'http://testdomain.com/',
            'paynet-order-id'   => 'PAYNET-112233',
            'serial-number'     =>  md5(time())
        );

        $assert                 = array
        (
            'state'             => Order::STATE_REDIRECT,
            'status'            => null
        );

        $dataset[]              = array($assert, $response);

        // ERROR
        $response               = array
        (
            'type'              => 'status-response',
            'status'            => 'error',
            'paynet-order-id'   => 'PAYNET-112233',
            'serial-number'     =>  md5(time()),
            'error-message'     => 'test error message',
            'error-code'        => '2'
        );

        $assert                 = array
        (
            'state'             => Order::STATE_END,
            'status'            => Order::STATUS_ERROR,
            'error_message'     => 'test error message',
            'error_code'        => '2',
            'exception'         => true
        );

        $dataset[]              = array($assert, $response);

        // Errors validation-error
        $response               = array
        (
            'type'              => 'validation-error',
            'error-message'     => 'test validation message',
            'error-code'        => '1'
        );

        $assert                 = array
        (
            'state'             => Order::STATE_END,
            'status'            => Order::STATUS_ERROR,
            'error_message'     => 'test validation message',
            'error_code'        => '1',
            'exception'         => true
        );

        $dataset[]              = array($assert, $response);

        // Errors validation-error
        $response               = array
        (
            'type'              => 'error',
            'error-message'     => 'test error message',
            'error-code'        => '1'
        );

        $assert                 = array
        (
            'state'             => Order::STATE_END,
            'status'            => Order::STATUS_ERROR,
            'error_message'     => 'test error message',
            'error_code'        => '1',
            'exception'         => true
        );

        $dataset[]              = array($assert, $response);

        return $dataset;
    }

    public function testProcess($assert, $callback)
    {
        $e = null;
        try
        {
            $response = $this->query->processOrder($this->order, $callback);
        }
        catch(PaynetException $e)
        {
        }

        if(!empty($assert['exception']))
        {
            $e = $this->order->getLastError();

            $this->assertInstanceOf('PaynetEasy\Paynet\Exception\PaynetException', $e, 'expected exception PaynetException');
            $this->assertEquals($assert['error_message'], $e->getMessage(), 'exception message mismatch');
            $this->assertEquals($assert['error_code'], $e->getCode(), 'exception code mismatch');

            return;
        }
        elseif(!empty($assert['error_message']) && $assert['status'] !== 'declined')
        {
            $e = $this->order->getLastError();

            $this->assertInstanceOf('PaynetEasy\Paynet\Exception\PaynetException', $e, 'expected getLastError');
            $this->assertEquals($assert['error_message'], $e->getMessage(), 'Error Message wrong');
            $this->assertEquals($assert['error_code'], $e->getCode(), 'Error Code wrong');
        }
        else
        {
            $this->assertNotInstanceOf('PaynetEasy\Paynet\Exception\PaynetException', $e, 'not expected exception PaynetException');
            $this->assertNotInstanceOf('PaynetEasy\Paynet\Exception\PaynetException',
                                        $this->order->getLastError(),
                                       'getLastError must be null');
            $this->assertInstanceOf('PaynetEasy\Paynet\Transport\Response', $response);
        }

        if (isset($assert['state']))
        {
            $this->assertEquals($assert['state'], $this->order->getState(), 'query.state not equal ' . $assert['state']);
        }

        if (isset($assert['status']))
        {
            $this->assertEquals($assert['status'], $this->order->getStatus(), 'query.status not equal ' . $assert['status']);
        }
    }
}
