<?php

class WsEpayTest extends PHPUnit_Framework_TestCase
{
    /** WsEpay */
    private $wsEpay = null;
    protected function setUp()
    {
        require_once __DIR__ . '/WsEpay.php';
        $merchantnumber = 8008627;
        $this->wsEpay = new WsEpay($merchantnumber);
    }

    public function testGetTransactionInformation() {
        $transactId = 9578927;
        $transaction = $this->wsEpay->getTransactionInformation($transactId);
        if(!$transaction) {
            $this->fail($this->wsEpay->error);
        }
        $this->assertObjectHasAttribute('gettransactionResult', $transaction, "We didn't get a properly formatted response");
        $this->assertTrue($transaction->gettransactionResult, "Transaction failed");
    }

    public function testCaptureTooHighAmount() {
        $transactId = 9578868;
        $amount = 1000000;
        try {
            $transaction = $this->wsEpay->capture($transactId,$amount);

            if(!$transaction) {
                $this->fail($this->wsEpay->error);
            }
        } catch( \EpayResponseException $e) {
            $this->assertNotEquals($e->getCode(),-1021, "Please wait 15 minutes");
        }
    }

    /**
     * @expectedException EpayResponseException
     */
    public function testGetTransactionInformationNonExistingTransaction() {
        $transactId = 3423143242;

        $transaction = $this->wsEpay->getTransactionInformation($transactId);
        if(!$transaction) {
            $this->fail($this->wsEpay->error);
        }

        $this->assertObjectHasAttribute('gettransactionResult', $transaction, "We didn't get a properly formatted response");
        $this->assertFalse($transaction->gettransactionResult, "Transaction was successful.");

    }

    /**
     * @expectedException EpayResponseException
     */
    public function testCaptureFailure() {
        $transactId = 3423123123;
        $amount = 1000;
        $capture = $this->wsEpay->capture($transactId,$amount);
        $this->assertObjectHasAttribute('captureResult',$capture,"We didn't get a properly formatted response");
        $this->assertFalse($capture->captureResult, "The capture was successfull.");
    }

   /* private function assertNotEquals($o1,$o2) {
        $this->assertThat(
            $o1,
            $this->logicalNot(
                $this->equalTo($o2)
            )
        );
    } */
}