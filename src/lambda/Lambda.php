<?php

require_once dirname(__FILE__) . '/Request.php';
require_once dirname(__FILE__) . '/Response.php';

class Lambda
{
    /**
     * LambdaのコンテナにPOSTする
     * 
     * @param Request $request
     * @return Response|null
     */
    public static function send(Request $request) : ?Response
    {
        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => getenv('LAMBDA_REQUEST_URL'),
                CURLOPT_HEADER => false,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($request, JSON_UNESCAPED_SLASHES),
                CURLOPT_RETURNTRANSFER => true
            ]);

            $responseStr = curl_exec($ch);
            if ($responseStr === false) {
                throw new RuntimeException('送信失敗 : ' . curl_error($ch));
            }

            return new Response($responseStr);
        } catch (Exception $e) {
            throw $e;
        } finally {
            if ($ch) {
                curl_close($ch);
            }
        }
    }

    /**
     * Lambdaのコンテナから受け取ったResponseを元に
     * このプロキシとしてのレスポンスを返す
     * 
     * @param Response $response
     * @return void
     */
    public static function back(Response $response) : void
    {
        
    }

    public static function response(int $code, string $body, array $headers = [], array $cookies = []) : void
    {
        // header('HTTP/2 ' . $code); // TODO HTTP2にしたいけど・・
        http_response_code($code);

        foreach ($headers as $key => $val) {
            header($key . ': ' . $val, false);
        }

        header('content-length: ' . strlen($body));

        // TODO http拡張入れるのはダルいわパーサー作るのはダルいわで
        // foreach ($cookies as $val) {
        //     setcookie($val);
        // }

        echo $body;

        exit;
    }
}
