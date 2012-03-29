<?php

class wsEpayTest extends PHPUnit_Framework_TestCase
{
    private $wsEpay = null;
    protected function setUp()
    {
        require_once __DIR__ . '/wsEpay.php';
        $merchantnumber = 0;
        $this->wsEpay = new wsEpay($merchantnumber/* Missing Merchant number */);
    }

    public function testGetTransactionInformation() {
        $this->fail('Not implemented');
    }
}