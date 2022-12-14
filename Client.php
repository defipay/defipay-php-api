<?php

namespace Defipay\api;

use Elliptic\EC;
use PHPUnit\Runner\Exception;


class Client
{
    private $apiSigner;
    private $apiKey;
    private $defipayPub;
    private $host;
    private $debug;

    public function __construct(ApiSigner $apiSigner, array $config, bool $debug = false)
    {
        $this->apiKey = $apiSigner->getPublicKey();
        $this->apiSigner = $apiSigner;
        $this->defipayPub = $config['defipayPub'];
        $this->host = $config['host'];
        $this->debug = $debug;
    }

    function createOrder(string $notifyUrl, string $returnUrl
                            , string $memberTransNo, string $amount
                            , string $currency, string $tokenIds){
        $params = [
            "notifyUrl" => $notifyUrl,
            "returnUrl" => $returnUrl,
            "memberTransNo" => $memberTransNo,
            "amount" => $amount,
            "currency" => $currency,
            "tokenIds" => $tokenIds
        ];
        return $this->request("POST", "/api-service/v1/external/pay/create", $params);
    }

    /**
     * @throws Exception
     */
    function request(string $method, string $path, array $data)
    {
        $ch = curl_init();
        $sorted_data = $this->sortData($data);
        $nonce = time() * 1000;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        $replace_path = substr_replace($path, "" ,0,12);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Biz-Api-Key:" . $this->apiKey,
            "Biz-Api-Nonce:" . $nonce,
            "Biz-Api-Signature:" . $this->apiSigner->sign(join("|", [$method, $replace_path, $nonce, $sorted_data]))
        ]);


        if ($method == "POST") {
            curl_setopt($ch, CURLOPT_URL, $this->host . $path);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else {
            curl_setopt($ch, CURLOPT_URL, $this->host . $path . "?" . $sorted_data);
        }
        if ($this->debug) {
            echo "request >>>>>>>>\n";
            echo join("|", [$method, $path, $nonce, $sorted_data]), "\n";
        }

        list($header, $body) = explode("\r\n\r\n", curl_exec($ch), 2);
        curl_close($ch);
        return json_decode($body);
    }

    private function sortData(array $data): string
    {
        ksort($data);
        $result = [];
        foreach ($data as $key => $val) {
            array_push($result, $key . "=" . $val);
        }
        return join("&", $result);
    }

    function verifyEcdsa(string $message, string $timestamp, string $signature): bool
    {
        $message = hash("sha256", hash("sha256", "$message|$timestamp", True), True);
        $ec = new EC('secp256k1');
        $key = $ec->keyFromPublic($this->defipayPublicKey, "hex");
        return $key->verify(bin2hex($message), $signature);
    }

    function queryOrder(string $transNo){
        $params = [
            "transNo" => $transNo
        ];
        return $this->request("POST", "/api-service/v1/external/pay/query", $params);
    }

    function createPayoutOrder(string $notifyUrl, string $memberTransNo, string $amount , string $currency, string $toAddress, int $tokenId, string $payAmount = null){
        $params = [
            "notifyUrl" => $notifyUrl,
            "memberTransNo" => $memberTransNo,
            "amount" => $amount,
            "currency" => $currency,
            "toAddress" => $toAddress,
            "tokenId" => $tokenId,
            "payAmount" => $payAmount
        ];
        return $this->request("POST", "/api-service/v1/external/payout/create", $params);
    }

    function queryPayoutOrder( string $transNo){
        $params = [
            "transNo" => $transNo
        ];
        return $this->request("POST", "/api-service/v1/external/payout/query", $params);
    }

    function queryBillCurrency( int $offset , int $limit){
        $params = [
            "offset" => $offset,
            "limit" => $limit
        ];
        return $this->request("POST", "/api-service/v1/external/billCurrency/query", $params);
    }

    function queryPayCurrency( int $offset , int $limit){
        $params = [
            "offset" => $offset,
            "limit" => $limit
        ];
        return $this->request("POST", "/api-service/v1/external/token/query", $params);
    }

    function queryCryptoAmount(){
        return $this->request("GET", "/api-service/v1/external/account/query", []);
    }

    function queryOrderList(int $offset , int $limit){
        $params = [
            "offset" => $offset,
            "limit" => $limit
        ];
        return $this->request("POST", "/api-service/v1/external/order/list", $params);
    }

    function getOrderDetail(string $transNo){
        $params = [
            "transNo" => $transNo
        ];
        return $this->request("GET", "/api-service/v1/external/order/getDetail", $params);
    }

    function queryRate(string $base , string $quote){
        $params = [
            "base" => $base,
            "quote" => $quote
        ];
        return $this->request("POST", "/api-service/v1/external/rate/query", $params);
    }
}
