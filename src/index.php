<?php

require_once './lambda/Request.php';
require_once './lambda/Lambda.php';

ini_set('error_log', 'php://stderr');

try {
    $request = new Request();
    // var_dump(json_encode($request, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
    $response = Lambda::send($request);
    Lambda::back($response);
} catch (Throwable $e) {
    error_log($e);
    Lambda::response(500, 'server error');
}
