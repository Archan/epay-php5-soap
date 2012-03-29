<?php

class wsEpayTest extends PHPUnit_Framework_TestCase
{
    /** wsEpay */
    private $wsEpay = null;
    protected function setUp()
    {
        require_once __DIR__ . '/wsEpay.php';
        $merchantnumber = 8008627;
        $this->wsEpay = new wsEpay($merchantnumber);
    }

    public function testGetTransactionInformationNonExistingTransaction() {
        $transactId = 3423143242;
        $transaction = $this->wsEpay->getTransactionInformation($transactId);
        if(!$transaction) {
            $this->fail($this->wsEpay->error);
        }

        $this->assertObjectHasAttribute('gettransactionResult', $transaction, "We didn't get a properly formatted response");
        $this->assertFalse($transaction->gettransactionResult, "Transaction was successful.");

    }

    public function testCaptureFailure() {
        $transactId = 3423123123;
        $amount = 1000;
        $capture = $this->wsEpay->capture($transactId,$amount);
        $this->assertObjectHasAttribute('captureResult',$capture,"We didn't get a properly formatted response");
        $this->assertFalse($capture->captureResult, "The capture was successfull.");
    }
}