<?php
require __DIR__ . "/vendor/autoload.php";

use Defipay\api\Client;
use Defipay\api\Config;
use Defipay\api\LocalSigner;
use PHPUnit\Framework\TestCase;

require "LocalSigner.php";
require "Client.php";
require "Config.php";


class ClientTest extends TestCase{
    const apiSecret = "14b8edb45dee3f5fb6bf06110dd0d6267a96a4e899f518517e9f8bcb1d9ce05c";
    private $client;

    /**
     * @throws Exception
     */
    public function testCreateOrder()
    {
        $res = $this->client->createOrder("http://xcsewvb.ao/nhhcn", "http://xcsewvb.ao/nhhcn", "test122234124",
            "1000","USDT", "2");
        echo json_encode($res);
        $this->assertTrue($res->success);
    }

    /**
     * @throws Exception
     */
    public function testQueryOrder()
    {
        $res = $this->client->queryOrder( "YBNROUDC");
        echo json_encode($res);
        $this->assertTrue($res->success);
    }

    /**
     *
     * @throws Exception
     */
    public function testCreatePayoutOrder()
    {
        $res = $this->client->createPayoutOrder("http://xcsewvb.ao/nhhcn", "payouttest111", "1000", "USDT",
        "0x3531C5F7540aDC5e5d640De11DE524cD379CC717", 2, null);
        echo json_encode($res);
        $this->assertTrue($res->success);
    }

    /**
     * @throws Exception
     */
    public function testQueryPayoutOrder()
    {
        $res = $this->client->queryPayoutOrder("C5LOTLWK");
        echo json_encode($res);
        $this->assertTrue($res->success);
    }

    /**
     * @throws Exception
     */
    public function testQueryBillCurrency()
    {
        $res = $this->client->queryBillCurrency(1,10);
        echo json_encode($res);
        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     */
    public function testQueryPayCurrency()
    {
        $res = $this->client->queryPayCurrency(1,10);
        echo json_encode($res);
        $this->assertTrue($res->success);
    }

    /**
     * @throws Exception
     */
    public function testQueryCryptoAmount()
    {
        $res = $this->client->queryCryptoAmount();
        echo json_encode($res);
        $this->assertTrue($res->success);
    }

    /**
     * @throws Exception
     */
    public function testQueryOrderList()
    {
        $res = $this->client->queryOrderList(1,2);
        echo json_encode($res);
        $this->assertTrue($res->success);
    }

    /**
     * @throws Exception
     */
    public function testGetOrderDetail()
    {
        $res = $this->client->getOrderDetail("29N3FVHO");
        echo json_encode($res);
        $this->assertTrue($res->success);
    }

    /**
     * @throws Exception
     */
    public function testQueryRate()
    {
        $res = $this->client->queryRate("ETH","USDT");
        echo json_encode($res);
        $this->assertTrue($res->success);
    }

    public function testGenerateKeyPair()
    {
        $key = LocalSigner::generateKeyPair();
        echo "apiSecret:", $key['apiSecret'], "\n";
        echo "apiKey:", $key['apiKey'];
        $this->assertTrue(true);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $signer = new LocalSigner(self::apiSecret);
        $this->client = new Client($signer, Config::SANDBOX, false);
    }
}