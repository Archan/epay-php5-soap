<?php

class wsEpay
{
    private $merchantnumber;
    private $client;
    private $encoding;

    public function __construct($merchantnumber, $encoding = 'UTF-8')
    {
        $this->client = new SoapClient("https://ssl.ditonlinebetalingssystem.dk/remote/payment.asmx?WSDL", array('encoding' => $encoding));
        $this->encoding = $encoding;
        $this->merchantnumber = $merchantnumber;
    }

    public function getTransactionInformation($transactionId) {
        try
        {
            $params = array(
                'merchantnumber' => $this->merchantnumber,
                'transactionid' => $transactionId
            );
            $transaction = $this->client->gettransactioninformation($params);

            return $transaction->GetAllParcelShopsResult->PakkeshopData;
        }
        catch (Exception $e)
        {
            $this->error = __METHOD__ . ': ' . $e->getMessage();
            return false;
        }
    }
}
