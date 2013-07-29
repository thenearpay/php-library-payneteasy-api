<?php

namespace PaynetEasy\PaynetEasyApi\PaymentData;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-06-25 at 18:19:35.
 */
class PaymentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Payment
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Payment;
    }

    public function testSerializeAndUnserialize()
    {
        $this->object->setCustomer(new Customer(array
        (
            'first_name'    => 'Vasya',
            'last_name'     => 'Pupkin',
            'email'         => 'vass.pupkin@example.com',
        )));

        $this->object->setStatus(Payment::STATUS_CAPTURE);
        $this->object
            ->addPaymentTransaction(new PaymentTransaction(array
            (
                'status'    => PaymentTransaction::STATUS_APPROVED
            )))
            ->addPaymentTransaction(new PaymentTransaction(array
            (
                'status'    => PaymentTransaction::STATUS_DECLINED
            )));

        $unserializedPayment = unserialize(serialize($this->object));

        $this->assertEquals(Payment::STATUS_CAPTURE, $unserializedPayment->getStatus());
        $this->assertEquals('vass.pupkin@example.com', $unserializedPayment->getCustomer()->getEmail());

        $paymentTransactions = $unserializedPayment->getPaymentTransactions();

        $this->assertCount(2, $paymentTransactions);
        $this->assertEquals(PaymentTransaction::STATUS_APPROVED, reset($paymentTransactions)->getStatus());
        $this->assertEquals(PaymentTransaction::STATUS_DECLINED, end($paymentTransactions)->getStatus());

    }
}
