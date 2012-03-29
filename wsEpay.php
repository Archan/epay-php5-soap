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
    /**
     * @param $transactionId The transaction id of this request
     * @return bool|Object
     * @throws EpayResponseException Thrown if we couldn't find the transaction. (gettransactionResult == false)
     */
    public function getTransactionInformation($transactionId) {
        try
        {
            $params = array(
                'transactionid' => $transactionId,

            );
            $params = array_merge($this->api_params,$params);
            $transaction =  $this->client->gettransaction($params);
            if($transaction->gettransactionResult == false) {
                throw new EpayResponseException('Epay Response',$transaction->epayresponse);
            } else {
                return $transaction;
            }
        }
        catch (\SoapFault $e)
        {
            $this->error = __METHOD__ . ': ' . $e->getMessage();
            return false;
        }
    }
    /**

     * @param $transactionId The transaction id for the current request.
     * @param $amount The amount to caputre/
     * @param null $group optional parameter group
     * @return bool|Object Returns false if there was a SoapFault, and an Object otherwise.
     * @throws EpayException|EpayPbsResponseException|EpayResponseException These are thrown with regards to the error codes, use $e->getCode() to get the error code. In case of an EpayException, we don't know what happened!
     */
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
            $capture =  $this->client->capture($params);
            if($capture->captureResult == false) {
                if($capture->epayresponse != 0) {
                    throw new EpayResponseException('Epay Response',$capture->epayresponse);
                } else if ($capture->pbsResponse != 0) {
                    throw new EpayPbsResponseException('Pbs Response',$capture->pbsResponse);
                } else {
                    throw new EpayException('Unknown',0);
                }
            }
            return $capture;
        }
        catch (\SoapFault $e)
        {
            $this->error = __METHOD__ . ': ' . $e->getMessage();
            return false;
        }
    }
}

class EpayException extends \Exception {}
class EpayPbsResponseException extends EpayException {};
class EpayResponseException extends \EpayException {};
