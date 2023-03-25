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
    private string $rawQueryString;
    private array $cookies;
    private ?array $headers;
    private ?array $queryStringParameters;
    private array $requestContext;
    private string $body;
    private $pathParameters = null;
    private bool $isBase64Encoded;
    private $stageVariables = null;

    public function __construct()
    {
        $this->rawPath = explode('?', $_SERVER['REQUEST_URI'])[0];
        $this->rawQueryString = $_SERVER['QUERY_STRING'];
        $this->cookies = $_COOKIE;
        $this->setHeaders();
        $this->setQueryStringParameters();
        $this->setRequestContext();
        $this->setBody();
    }

    public function jsonSerialize() : array
    {
        return get_object_vars($this);
    }

    private function setHeaders() : void
    {
        $this->headers = [];

        foreach (getallheaders() as $key => $val) {
            $this->headers[strtolower($key)] = $val;
        }

        if (empty($this->headers)) $this->headers = null;
    }

    private function setQueryStringParameters() : void
    {
        $this->queryStringParameters = [];

        if ($this->rawQueryString) {
            foreach (explode('&', $this->rawQueryString) as $keyValStr) {
                $keyVal = explode('=', $keyValStr);
                $key = $keyVal[0];
                $val = $keyVal[1] ?? '';

                if (isset($this->queryStringParameters[$key])) {
                    $this->queryStringParameters[$key] .= ',' . $val;
                } else {
                    $this->queryStringParameters[$key] = $val;
                }
            }
        }

        if (empty($this->queryStringParameters)) $this->queryStringParameters = null;
    }

    private function setRequestContext() : void
    {
        $region = getenv('AWS_REGION') ?: 'us-west-2';
        $this->requestContext = [
            'accountId' => '123456789012',
            'apiId' => 'dummy12345',
            'authentication' => null,
            'authorizer' => null, // TODO 切り替えられるようにすべき
            'domainName' => 'dummy12345.lambda-url.' . $region  . '.on.aws',
            'domainPrefix' => 'dummy12345',
            'http' => [
                'method' => $_SERVER['REQUEST_METHOD'],
                'path' => $this->rawPath,
                'protocol' => $_SERVER['SERVER_PROTOCOL'],
                'sourceIp' => $_SERVER['REMOTE_ADDR'],
                'userAgent' => $_SERVER['HTTP_USER_AGENT'],
            ],
            'requestId' => 'dummyfd5-9e7b-434f-bd42-4f8fa224b599',
            'routeKey' => '$default',
            'stage' => '$default',
            'time' => date('d/M/Y:H:i:s O'),
            'timeEpoch' => time(),
        ];
    }

    private function setBody() : void
    {
        $input = file_get_contents('php://input');

        if (empty($input)) {
            $this->body = '';
            $this->isBase64Encoded = false;
            return;
        }

        if (mb_detect_encoding($input, 'ASCII', true) === false) {
            // ASCII文字以外が含まれる場合、バイナリデータと判定する
            $this->body = base64_encode($input);
            $this->isBase64Encoded = true;
        } else {
            $this->body = $input;
            $this->isBase64Encoded = false;
        }
    }
}
