<?php


namespace App\Support;


use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class CurlSupport
{
    public static function post($url, $data = [], $headers = [], &$cookieJar = null)
    {
        if (!$cookieJar) {
            $cookieJar = new CookieJar();
        }

        $response = (new Client([
            'timeout' => 10,
            'cookies' => $cookieJar
        ]))->request('POST', $url, [
            'form_params' => $data,
            'headers' => $headers
        ]);

        $cookieJar = $cookieJar;
        return $response->getBody()->getContents();
    }

    public static function get($url, $data = [], $headers = [], $cookieJar = null)
    {
        if (!$cookieJar) {
            $cookieJar = new CookieJar();
        }
        $response = (new Client([
            'timeout' => 30,
            'cookies' => $cookieJar
        ]))->request('GET', $url, [
            'query' => $data,
            'headers' => $headers
        ]);

        return $response->getBody()->getContents();
    }

    public static function curlPost($url, $postData, $cookie = null)
    {
        $header = array(
            'Accept: application/json',
        );

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
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);

        // 超时设置，以毫秒为单位
        // curl_setopt($curl, CURLOPT_TIMEOUT_MS, 500);

        // 设置请求头
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE );

        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        //执行命令
        $data = curl_exec($curl);

        curl_close($curl);

        return $data;
    }
}
