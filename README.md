# lambda-function-url-proxy

## 概要

ローカルで[Lambda Function URL](https://docs.aws.amazon.com/ja_jp/lambda/latest/dg/lambda-urls.html)を想定してLambdaを開発する時、  
Lambdaへのリクエスト内容から[リクエストペイロードの形式](https://docs.aws.amazon.com/ja_jp/lambda/latest/dg/urls-invocation.html#urls-request-payload)のリクエストボディに変換して大元のLambdaに再送し、  
返ってきた[レスポンスペイロードの形式](https://docs.aws.amazon.com/ja_jp/lambda/latest/dg/urls-invocation.html#urls-response-payload)であるレスポンスから実際のHTTPレスポンスに変換するためのプロキシとなるコンテナです。

## 注意

あくまでローカル環境用です。本番環境等では使わない方が良いでしょう。
