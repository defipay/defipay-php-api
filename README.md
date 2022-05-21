# Defipay php API

defipay-php-api 是一個輕量級的 php 庫，用於與[Defipay API](http://doc.defipay.biz/)交互，提供完整的 API 覆蓋。


* [安裝](#安裝)
* [測試](#測試)
* [用法](#用法)
    * [初始化](#初始化)
        * [生成密鑰對](#生成密鑰對)
        * [初始化 RestClient](#初始化RestClient)
        * [初始化 ApiSigner](#初始化-apisigner)
    * [充值](#充值)
        * [充值請求下單](#充值請求下單)
        * [充值交易查詢](#充值交易查詢)
    * [提現](#提現)
        * [提現請求下單](#提現請求下單)
        * [提現交易查詢](#提現交易查詢)
    * [交易](#交易)
        * [獲取已入賬的交易詳情](#獲取已入賬的交易詳情)
        * [通過ID查詢獲取已確認的交易記錄](#通過ID查詢獲取已確認的交易記錄)
    * [賬戶余額查詢](#賬戶余額查詢)
        * [商戶賬戶余額查詢](#商戶賬戶余額查詢)
        * [支持賬單幣種查詢](#支持賬單幣種查詢)
        * [支持支付幣種查詢](#支持支付幣種查詢)
    * [匯率查詢](#匯率查詢)
        * [幣種匯率查詢](#幣種匯率查詢)

## 安裝

添加依賴項 `composer.json`

``` json
"require": {
    ...
    "defipay/api": "*",
  }
```

## 測試

```
php phpunit.phar ClientTest.php
```

## 用法

### 初始化

#### 生成密鑰對
```php
<?php
require 'vendor/autoload.php';

use Defipay\api\Client;
use Defipay\api\Config;
use Defipay\api\LocalSigner;
$key = LocalSigner::generateKeyPair();
echo "apiSecret:", $key['apiSecret'],"\n";
echo "apiKey:", $key['apiKey'];
```

#### 初始化RestClient

```php
<?php
require 'vendor/autoload.php';

use Defipay\api\Client;
use Defipay\api\Config;
use Defipay\api\LocalSigner;

$client = new Client($signer, Config::SANDBOX, false);
```

#### 初始化-apiSigner


`ApiSigner` 可以通過實例化 `new LocalSigner("secretkey" )`

在某些情況下，您的私鑰無法導出，例如，您的私鑰在 aws kms 中，您應該通過實現`ApiSigner`接口傳入您自己的實現：

### 充值

#### 充值請求下單
```php
$client->createOrder("http://xcsewvb.ao/nhhcn", "http://xcsewvb.ao/nhhcn", "test1234124", "1000","USDT", "2");
```
<details>
<summary>響應視圖</summary>


```php
{"code":200,"msg":"OK","data":{"cashierUrl":"http:\/\/www.defipay.biz\/customer\/#\/order\/1SQKTD17","tokenInfo":[],"memberTransNo":"test122234124","currency":"USDT","currencyLogoUrl":"https:\/\/defipay-test.oss-cn-hangzhou.aliyuncs.com\/defipay_v_1.0\/1b88c0c2dba04080bd3165843de3ffae.png","amount":"1000","transNo":"1SQKTD17"},"success":true}
```
</details>

#### 充值交易查詢
```php
$client->queryOrder("YBNROUDC");
```
<details>
<summary>響應視圖</summary>


```php
{"code":200,"msg":"OK","data":{"id":null,"transNo":"YBNROUDC","memberTransNo":"test1234124","memberSeq":"80000029","amount":"1000","currency":"USDT","toAddress":"","shortName":"","billAmount":"","state":"300","createTime":"1651905450","settleTime":"0"},"success":true}
```
</details>


### 提現
#### 提現請求下單
```php
$client->createPayoutOrder("http://xcsewvb.ao/nhhcn", "payouttest111", "1000", "USDT",
        "0x3531C5F7540aDC5e5d640De11DE524cD379CC717", 2, null);
```
<details>
<summary>響應視圖</summary>

```php
{"code":200,"msg":"OK","data":{"transNo":"C5LOTLWK","memberTransNo":"payouttest111","currency":"USDT","amount":"1000","tokenId":2,"tokenAmount":"0.374510748524758788"},"success":true}
```
</details>

#### 提現交易查詢
```php
$client->queryPayoutOrder("C5LOTLWK");
```
<details>
<summary>響應視圖</summary>


```php
{"code":200,"msg":"OK","data":{"id":null,"transNo":"C5LOTLWK","memberTransNo":"payouttest111","memberSeq":"80000029","amount":"1000","currency":"USDT","toAddress":"0x3531C5F7540aDC5e5d640De11DE524cD379CC717","shortName":"ETH","billAmount":"0.374510748524758788","state":"300","createTime":"1651906147","settleTime":"0"},"success":true}
```
</details>

### 交易

#### 獲取已入賬的交易詳情
```php
$client->queryOrderList(1,10);
```
<details>
<summary>響應視圖</summary>


```php
{"code":200,"msg":"OK","data":[{"id":4546,"transNo":"C5LOTLWK","memberTransNo":"payouttest111","memberSeq":"80000029","amount":"1000","currency":"USDT","toAddress":"0x3531C5F7540aDC5e5d640De11DE524cD379CC717","shortName":"ETH","billAmount":"0.374510748524758788","state":"300","createTime":"1651906147","settleTime":"0"},{"id":3170,"transNo":"1SQKTD17","memberTransNo":"test122234124","memberSeq":"80000029","amount":"1000","currency":"USDT","toAddress":"","shortName":"","billAmount":"","state":"300","createTime":"1651905865","settleTime":"0"}],"success":true}
```
</details>

#### 通過ID查詢獲取已確認的交易記錄
```php
$client->getOrderDetail("29N3FVHO");
```
<details>
<summary>響應視圖</summary>


```php
{"code":200,"msg":"OK","data":{"id":2873,"transNo":"29N3FVHO","memberTransNo":"202205050000000001","memberSeq":"80000020","amount":"0.1","currency":"ETH","toAddress":"0x8034761a6b9b2aa11f5b5fc9c5539a8061e0d759","shortName":"ETH","billAmount":"0.1","state":"200","createTime":"1651750925","settleTime":"1651754339"},"success":true}
```
</details>

### 賬戶余額查詢
#### 商戶賬戶余額查詢
```php
$client->queryCryptoAmount();
```
<details>
<summary>響應視圖</summary>


```php
{"code":200,"msg":"OK","data":[{"totalAmount":0,"frozenAmount":0,"availableAmount":0,"tokenId":2,"shortName":"ETH","name":"Ether","displayName":"ETH","logoUrl":"https:\/\/defipay-test.oss-cn-hangzhou.aliyuncs.com\/defipay_v_1.0\/ba40da70bed74489a7ed6adaed495763.png"},{"totalAmount":0,"frozenAmount":0,"availableAmount":0,"tokenId":3,"shortName":"USDT","name":"Tether","displayName":"USDT-ERC20","logoUrl":"https:\/\/defipay-test.oss-cn-hangzhou.aliyuncs.com\/defipay_v_1.0\/1b88c0c2dba04080bd3165843de3ffae.png"}],"success":true}
```
</details>

#### 支持賬單幣種查詢
```php
$client->queryBillCurrency(1,10);
```
<details>
<summary>響應視圖</summary>


```php
{"code":200,"msg":"OK","data":[{"assertId":"AED","typeIsCrypto":0},{"assertId":"AUD","typeIsCrypto":0},{"assertId":"BRC","typeIsCrypto":0},{"assertId":"BYN","typeIsCrypto":0},{"assertId":"CAD","typeIsCrypto":0},{"assertId":"CHF","typeIsCrypto":0},{"assertId":"CLP","typeIsCrypto":0},{"assertId":"CNY","typeIsCrypto":0},{"assertId":"DEM","typeIsCrypto":0},{"assertId":"DKK","typeIsCrypto":0}],"success":true}
```
</details>

#### 支持支付幣種查詢
```php
$client->queryPayCurrency(1,10);
```
<details>
<summary>響應視圖</summary>


```php
{"code":200,"msg":"OK","data":[{"id":1,"name":"BTC","displayName":"BTC","shortName":"BTC","logoUrl":"https:\/\/defipay-test.oss-cn-hangzhou.aliyuncs.com\/defipay_v_1.0\/8f6e5e2382f94028b87307ad5c73c52e.png","chainAssertId":null,"chainAssertDecimal":"6"},{"id":2,"name":"Ether","displayName":"ETH","shortName":"ETH","logoUrl":"https:\/\/defipay-test.oss-cn-hangzhou.aliyuncs.com\/defipay_v_1.0\/ba40da70bed74489a7ed6adaed495763.png","chainAssertId":null,"chainAssertDecimal":"18"},{"id":3,"name":"Tether","displayName":"USDT-ERC20","shortName":"USDT","logoUrl":"https:\/\/defipay-test.oss-cn-hangzhou.aliyuncs.com\/defipay_v_1.0\/1b88c0c2dba04080bd3165843de3ffae.png","chainAssertId":"0xdac17f958d2ee523a2206206994597c13d831ec7","chainAssertDecimal":"6"},{"id":4,"name":"Binance Coin","displayName":"BNB","shortName":"BNB","logoUrl":"https:\/\/defipay-test.oss-cn-hangzhou.aliyuncs.com\/defipay_v_1.0\/6300d391da1642c58c6673f32235db89.png","chainAssertId":null,"chainAssertDecimal":"18"},{"id":5,"name":"USDC","displayName":"USDC-ERC20","shortName":"USDC","logoUrl":"https:\/\/defipay-test.oss-cn-hangzhou.aliyuncs.com\/defipay_v_1.0\/b5afa9c1d46842cea775d3e63c9287b7.png","chainAssertId":"0xa0b86991c6218b36c1d19d4a2e9eb0ce3606eb48","chainAssertDecimal":"6"},{"id":6,"name":"Ripple","displayName":"XRP-BEP20","shortName":"XRP","logoUrl":"https:\/\/defipay-test.oss-cn-hangzhou.aliyuncs.com\/defipay_v_1.0\/7df1c4bc20054894b52b4aa6a7ae1e81.png","chainAssertId":"0x1d2f0da169ceb9fc7b3144628db156f3f6c60dbe","chainAssertDecimal":"18"},{"id":7,"name":"Cardano","displayName":"ADA-BEP20","shortName":"ADA","logoUrl":"https:\/\/defipay-test.oss-cn-hangzhou.aliyuncs.com\/defipay_v_1.0\/35d6f3167e9c4a9dae0ef34c50a1deb0.png","chainAssertId":"0x3ee2200efb3400fabb9aacf31297cbdd1d435d47","chainAssertDecimal":"18"},{"id":8,"name":"Solana","displayName":"SOL","shortName":"SOL","logoUrl":"https:\/\/defipay-test.oss-cn-hangzhou.aliyuncs.com\/defipay_v_1.0\/068abeb1076c40189d64c69cf70ed6e6.png","chainAssertId":null,"chainAssertDecimal":"18"},{"id":9,"name":"Luna Coin","displayName":"LUNA","shortName":"LUNA","logoUrl":"https:\/\/defipay-test.oss-cn-hangzhou.aliyuncs.com\/defipay_v_1.0\/a8a96dd9240c425291bb87178aae935c.png","chainAssertId":null,"chainAssertDecimal":"18"},{"id":10,"name":"AVAX","displayName":"AVAX-C","shortName":"AVAX","logoUrl":"https:\/\/defipay-test.oss-cn-hangzhou.aliyuncs.com\/defipay_v_1.0\/62f25f5cf608415587a7cda95d9238c0.png","chainAssertId":null,"chainAssertDecimal":"18"}],"success":true}
```
</details>


### 匯率查詢
#### 幣種匯率查詢
```php
$client->queryRate("ETH","USDT");
```
<details>
<summary>響應視圖</summary>


```php
{"code":200,"msg":"OK","data":{"rate":"2669.8356867640886695401071913","rateTime":1651896014},"success":true}
```
</details>


