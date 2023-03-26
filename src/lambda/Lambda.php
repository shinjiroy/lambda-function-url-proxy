<?php

require_once dirname(__FILE__) . '/Request.php';
require_once dirname(__FILE__) . '/Response.php';

class Lambda
{
    /**
     * Lambdaのコンテナに実際のリクエストを送信する
     * 
     * @param Request $request
     * @return Response|null
     */
    public static function send(Request $request) : ?Response
    {
        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $request->getUrl(),
                CURLOPT_HEADER => false,
                constant('CURLOPT_' . $request->getMethod()) => true,
                CURLOPT_POSTFIELDS => json_encode($request, JSON_UNESCAPED_SLASHES),
                CURLOPT_RETURNTRANSFER => true
            ]);

            $responseStr = curl_exec($ch);
            if (curl_errno($ch) !== 0) {
                throw new RuntimeException('curl failed curl_errno: ' . curl_errno($ch) . ', curl_error:' . curl_error($ch));
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
        self::response(
            $response->getStatusCode(),
            $response->getBody(),
            $response->getHeaders(),
            $response->getCookies(),
        );
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
