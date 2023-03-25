<?php

/**
 * Lambda Function URLのリクエストペイロード変換器
 * 
 * https://docs.aws.amazon.com/ja_jp/lambda/latest/dg/urls-invocation.html#urls-request-payload
 */
class Request implements JsonSerializable
{
    private string $version = '2.0';
    private string $routeKey = '$default';
    private string $rawPath;

    public function __construct()
    {
        var_dump($_SERVER);
    }

    public function jsonSerialize() : array
    {
        return get_object_vars($this);
    }
}
