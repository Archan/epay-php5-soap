<?php

class wsEpay
{
    private $merchantnumber;
    private $client;
    private $encoding;

    public function __construct($merchantnumber, $pwd = false, $encoding = 'UTF-8')
    {
        $this->client = new SoapClient("https://ssl.ditonlinebetalingssystem.dk/remote/payment.asmx?WSDL", array('encoding' => $encoding, 'trace'  => 1));
        $this->encoding = $encoding;
        $this->api_params = array(
            'merchantnumber' => $merchantnumber,
            'epayresponse' => 1 /* Thanks for messing up everything epay, this value isn't required according to your own implementation? */
        );
        if($pwd) {
            $this->api_params = array_merge($this->api_params,array('pwd' => $pwd));
        }
        return $this;
    }

    public function getTransactionInformation($transactionId) {
        try
        {
            $params = array(
                'transactionid' => $transactionId,

            );
            $params = array_merge($this->api_params,$params);
            return $this->client->gettransaction($params);
        }
        catch (Exception $e)
        {
            $this->error = __METHOD__ . ': ' . $e->getMessage();
            return false;
        }
    }

    public function capture($transactionId, $amount, $group = null) {
        try
        {
            $params = array(
                'transactionid' => $transactionId,
                'amount' => $amount,
                'pbsResponse' => 1
            );
            if(!is_null($group)) {
                $params = array_merge(array('group' => $group, $params));
            }
            $params = array_merge($this->api_params,$params);
            return $this->client->capture($params);
        }
        catch (Exception $e)
        {
            $this->error = __METHOD__ . ': ' . $e->getMessage();
            return false;
        }
    }




}
