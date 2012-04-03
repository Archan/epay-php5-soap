<?php

class WsEpay
{
    const LANG_DA = 1;
    const LANG_EN = 2;
    const LANG_SE = 3;
    private $api_params = array();
    private $client;
    private $encoding;



    public function __construct($merchantnumber, $pwd = false, $encoding = 'UTF-8')
    {
        $this->client = new SoapClient("https://ssl.ditonlinebetalingssystem.dk/remote/payment.asmx?WSDL", array('encoding' => $encoding, 'trace'  => 1));
        $this->encoding = $encoding;
        $this->api_params = array(
            'merchantnumber' => $merchantnumber,
            'epayresponse' => 0 /* Thanks for messing up everything epay, this value isn't required according to your own implementation? */
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
                'pbsResponse' => 0
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
    /**
     * @param $transactionId
     * @param $amount
     * @param null $group
     * @return bool
     * @throws EpayException|EpayPbsResponseException|EpayResponseException
     */
    public function credit($transactionId, $amount, $group = null) {
        try
        {
            $params = array(
                'transactionid' => $transactionId,
                'amount' => $amount,
                'pbsResponse' => 0
            );
            if(!is_null($group)) {
                $params = array_merge(array('group' => $group, $params));
            }
            $params = array_merge($this->api_params,$params);
            $credit =  $this->client->credit($params);
            if($credit->creditResult == false) {
                if($credit->epayresponse != 0) {
                    throw new EpayResponseException('Epay Response',$credit->epayresponse);
                } else if ($credit->pbsResponse != 0) {
                    throw new EpayPbsResponseException('Pbs Response',$credit->pbsResponse);
                } else {
                    throw new EpayException('Unknown',0);
                }
            }
            return $credit;
        }
        catch (\SoapFault $e)
        {
            $this->error = __METHOD__ . ': ' . $e->getMessage();
            return false;
        }

    }
    /**
     * @param $transactionId
     * @param null $group
     * @return bool
     * @throws EpayException|EpayPbsResponseException|EpayResponseException
     */
    public function delete($transactionId, $group = null) {
        try
        {
            $params = array(
                'transactionid' => $transactionId,
                'pbsResponse' => 0
            );
            if(!is_null($group)) {
                $params = array_merge(array('group' => $group, $params));
            }
            $params = array_merge($this->api_params,$params);
            $delete =  $this->client->delete($params);
            if($delete->deleteResult == false) {
                if($delete->epayresponse != 0) {
                    throw new EpayResponseException('Epay Response',$delete->epayresponse);
                } else if ($delete->pbsResponse != 0) {
                    throw new EpayPbsResponseException('Pbs Response',$delete->pbsResponse);
                } else {
                    throw new EpayException('Unknown',0);
                }
            }
            return $delete;
        }
        catch (\SoapFault $e)
        {
            $this->error = __METHOD__ . ': ' . $e->getMessage();
            return false;
        }
    }

    public function getEpayError($errorCode, $language = self::LANG_DA) {
        try
        {
            $params = array(
                'Language' => $language,
                'Epayresponsecode' => $errorCode
            );
            $params = array_merge($this->api_params,$params);
            $epayerror =  $this->client->GetEpayError($params);
            if($epayerror->getEpayErrorResult == false) {
                if($epayerror->epayresponse != 0) {
                    throw new EpayResponseException('Epay Response',$epayerror->epayresponse);
                } else {
                    throw new EpayException('Unknown',0);
                }
            }
            return $epayerror;
        }
        catch (\SoapFault $e)
        {
            $this->error = __METHOD__ . ': ' . $e->getMessage();
            return false;
        }
    }
    public function getPbsError($errorCode, $language = self::LANG_DA) {
        try
        {
            $params = array(
                'Language' => $language,
                'Pbsresponsecode' => $errorCode
            );
            $params = array_merge($this->api_params,$params);
            $pbserror =  $this->client->GetPbsError($params);
            if($pbserror->getPbsErrorResult == false) {
                if($pbserror->pbsresponse != 0) {
                    throw new EpayPbsResponseException('Epay Pbs Response',$pbserror->pbsresponse);
                } else {
                    throw new EpayException('Unknown',0);
                }
            }
            return $epayerror;
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
