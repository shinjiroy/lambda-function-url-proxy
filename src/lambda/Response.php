<?php

/**
 * Lambda Function URLのレスポンスペイロード変換器
 * 
 * https://docs.aws.amazon.com/ja_jp/lambda/latest/dg/urls-invocation.html#urls-request-payload
 */
class Response implements JsonSerializable
{

    public function __construct()
    {

    }

    public function jsonSerialize() : array
    {
        return get_object_vars($this);
    }
}
