<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Query\Prototype\PaymentQueryTest;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\BillingAddress;
use PaynetEasy\PaynetEasyApi\PaymentData\Customer;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-06-18 at 18:17:58.
 */
class SaleFormQueryTest extends PaymentQueryTest
{
    /**
     * @var FormQuery
     */
    protected $object;

    protected $successType = 'async-form-response';

    protected $paymentStatus = Payment::STATUS_CAPTURE;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new SaleFormQuery('_');
    }

    public function testCreateRequestProvider()
    {
        return array(array
        (
            sha1
            (
                self::END_POINT .
                self::CLIENT_ID .
                9910 .
               'vass.pupkin@example.com' .
                self::SIGNING_KEY
            )
        ));
    }

    /**
     * @dataProvider testProcessResponseProcessingProvider
     */
    public function testProcessResponseProcessing(array $response)
    {
        list($paymentTransaction, $responseObject) = parent::testProcessResponseProcessing($response);

        $this->assertTrue($responseObject->isRedirectNeeded());

        return array($paymentTransaction, $responseObject);
    }

    public function testProcessResponseProcessingProvider()
    {
        return array(array(array
        (
            'type'              =>  $this->successType,
            'status'            => 'processing',
            'merchant-order-id' =>  self::CLIENT_ID,
            'paynet-order-id'   =>  self::PAYNET_ID,
            'serial-number'     =>  md5(time()),
            'redirect-url'      => 'http://redirect-url.com'
        )));
    }

    protected function getPayment()
    {
        return new Payment(array
        (
            'client_id'             => self::CLIENT_ID,
            'paynet_id'             => self::PAYNET_ID,
            'description'           => 'This is test payment',
            'amount'                =>  99.1,
            'currency'              => 'USD',
            'customer'              => new Customer(array
            (
                'first_name'            => 'Vasya',
                'last_name'             => 'Pupkin',
                'email'                 => 'vass.pupkin@example.com',
                'ip_address'            => '127.0.0.1',
                'birthday'              => '112681'
            )),
            'billing_address'       => new BillingAddress(array
            (
                'country'               => 'US',
                'state'                 => 'TX',
                'city'                  => 'Houston',
                'first_line'            => '2704 Colonial Drive',
                'zip_code'              => '1235',
                'phone'                 => '660-485-6353',
                'cell_phone'            => '660-485-6353'
            ))
        ));
    }
}
