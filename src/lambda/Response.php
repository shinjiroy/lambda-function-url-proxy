<?php

/**
 * Lambda Function URLのレスポンスペイロード変換器
 * 
 * https://docs.aws.amazon.com/ja_jp/lambda/latest/dg/urls-invocation.html#urls-response-payload
 */
class Response
{
    private int $statusCode;
    private array $headers;
    private string $body;
    private array $cookies;
    private bool $isBase64Encoded;

    public function __construct(string $responseStr)
    {
        $response = json_decode($responseStr, true);
        if (!$response) {
            throw new RuntimeException('invalid formatted response: ' .$responseStr);
        }

        $this->statusCode = $response['statusCode'];
        $this->headers = $response['headers'] ?? [];
        $this->body = $response['body'];
        $this->setCookies($response['cookies'] ?? []);
        $this->isBase64Encoded = $response['isBase64Encoded'] ?? false;
    }

    public function getStatusCode() : int
    {
        return $this->statusCode;
    }

    public function getHeaders() : array
    {
        return $this->headers;
    }

    public function getBody() : string
    {
        if ($this->isBase64Encoded) {
            // Base64エンコード済みならデコードして返す
            return base64_decode($this->body);
        } else if (json_decode($this->body)) {
            // JSON文字列ならそのJSON文字列をそのまま返す
            return $this->body;
        } else {
            // JSON文字列でなければ通常の文字列とみなし外側に"をつける
            return '"' . $this->body . '"';
        }
    }

    private function setCookies(array $strArray) : void
    {
        // TODO パーサー作るのがダルい
        $this->cookies = [];
    }

    public function getCookies() : array
    {
        return $this->cookies;
    }
}
