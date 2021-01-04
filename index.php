<?php
$clid = 'HGZmfGaq39WK6R7hZ4Q60HW9';
$scrt = 'a6tvjr8nAMKGGGI8D7tKz1vCapRi6P4t';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods:GET,POST,PUT,DELETE');
header('Access-Control-Allow-Headers:Origin, Content-Type, Cookie, Accept');
header('Access-Control-Allow-Credentials:true');

function curl_get($url){
    $curl = curl_init();
    //设置抓取的url
    curl_setopt($curl, CURLOPT_URL, $url);
    //设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER, 0);
    // 超时设置,以秒为单位
    curl_setopt($curl, CURLOPT_TIMEOUT, 1);

    // 超时设置，以毫秒为单位
    // curl_setopt($curl, CURLOPT_TIMEOUT_MS, 500);

    // 设置请求头
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    //执行命令
    $data = curl_exec($curl);

    // 显示错误信息
    if (curl_error($curl)) {
        return false;
    } else {
        curl_close($curl);
        return $data;
    }
}
function curl_post( $url, $postdata ) {
    //初始化
    $curl = curl_init();
    //设置抓取的url
    curl_setopt($curl, CURLOPT_URL, $url);
    //设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER, 0);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    // 超时设置
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);

    // 超时设置，以毫秒为单位
    // curl_setopt($curl, CURLOPT_TIMEOUT_MS, 500);

    // 设置请求头
    curl_setopt($curl, CURLOPT_HTTPHEADER, []);

    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE );
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE );

    //设置post方式提交
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
    //执行命令
    $data = curl_exec($curl);

    // 显示错误信息
    if (curl_error($curl)) {
        return "Error: " . curl_error($curl);
    } else {
        curl_close($curl);
        return $data;
    }
}

$text = $_GET['text'] ?? false;
if (!$text) {
    echo '';
} else {
    $curlRes = curl_post('https://tsn.baidu.com/text2audio', http_build_query([
        'tex' => urlencode($text),
        'tok' => '24.7bee991e4661c6395fdde43bf67024b4.2592000.1606361349.282335-22879781',
        'cuid' => 'a4:83:e7:b3:53:1b',
        'ctp' => '1',
        'lan' => 'zh'
    ]));
    $filename = 'result'.rand(111,222).'.mp3';
    file_put_contents($filename, $curlRes);
    echo $filename;
}