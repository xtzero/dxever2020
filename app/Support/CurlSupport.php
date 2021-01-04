<?php


namespace App\Support;


use GuzzleHttp\Client;

class CurlSupport
{
    public static function post($url, $data = [], $headers = [])
    {
        $response = (new Client([
           'timeout' => 30
        ]))->request('POST', $url, [
            'form_params' => $data,
            'headers' => $headers
        ]);

        return $response->getBody()->getContents();
    }

    public static function get($url, $data = [], $headers = [])
    {
        $response = (new Client([
            'timeout' => 30
        ]))->request('GET', $url, [
            'query' => $data,
            'headers' => $headers
        ]);

        return $response->getBody()->getContents();
    }
}
