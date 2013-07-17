<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\PaymentData\RecurrentCard;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-06-12 at 16:22:54.
 */
class MakeRebillQueryTest extends SaleQueryTest
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new MakeRebillQuery($this->getConfig());
    }

    public function testCreateRequestProvider()
    {
        return array(array
        (
            sha1
            (
                self::END_POINT .
                self::CLIENT_PAYMENT_ID .
                '99' .                          // amount
                self::RECURRENT_CARD_FROM_ID .
                self::SIGN_KEY
            )
        ));
    }

    public function getPayment()
    {
        $recurrentCard = new RecurrentCard(array
        (
            'cardrefid' => self::RECURRENT_CARD_FROM_ID,
            'cvv2'      => 123
        ));

        return parent::getPayment()
            ->setRecurrentCardFrom($recurrentCard);
    }
}
