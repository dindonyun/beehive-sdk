<?php
/**
 * Created 2019/4/8
 * User: LP
 * Date: 2019/4/8
 * Time: 17:05
 * Email: lp@hodorware.com
 */

function String2Hex($string){
    $hex='';
    for ($i=0; $i < strlen($string); $i++){
        $ascii = dechex(ord($string[$i]));
        if (strlen($ascii) < 2) {
            $ascii = '0'.$ascii;
        }
        $hex .= $ascii;
    }
    return $hex;
}
 
 
function Hex2String($hex){
    $string='';
    for ($i=0; $i < strlen($hex)-1; $i+=2){
        $string .= chr(hexdec($hex[$i].$hex[$i+1]));
    }
    return $string;
}

$time = time();

$appID = '5c3c1ed7aa2df50001b66682'; // 替换应用ID
$appSecret = 'Z5kjUResB21ktRoOGAHloFdBEEjRwuVE'; // 替换密钥
$salt = md5(uniqid(microtime(true),true));
$arr = [
    'appId' => $appID,
    'salt' => $salt,
    'timestamp' => $time
];
ksort($arr);
$data = json_encode($arr);
$data = sha1(json_encode($arr));
$key = substr(sha1($appSecret), 0, 32);
$secretData = openssl_encrypt(Hex2String($data), 'AES-128-ECB', Hex2String($key), OPENSSL_RAW_DATA, '');
$sign = String2Hex($secretData);
echo '$sign '.$sign;
echo PHP_EOL;
$requestConfig = [
    'base_uri' => 'https://www.fengchaoiot.com/api',
    'timeout' => 2.0
];

$client = new \GuzzleHttp\Client($requestConfig);
$bodyArr = [
    'appId' => $appID,
    'salt' => $salt,
    'timestamp' => $time,
    'sign' => $sign
];
//echo json_encode($bodyArr);exit;
$params['headers'] = [
    'Accept' => 'application/json',
    'X-Accept-Version' => 'beehive.v1'
];

$params['body'] = json_encode($bodyArr);

$response = $client->request('POST', '/accessToken', $params);
echo json_encode($response->getBody());

