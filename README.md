# lambda-function-url-proxy

## 概要

ローカルで[Lambda Function URL](https://docs.aws.amazon.com/ja_jp/lambda/latest/dg/lambda-urls.html)を想定してLambdaを開発する時、  
Lambdaへのリクエスト内容から[リクエストペイロードの形式](https://docs.aws.amazon.com/ja_jp/lambda/latest/dg/urls-invocation.html#urls-request-payload)のリクエストボディに変換して大元のLambdaに再送し、  
返ってきた[レスポンスペイロードの形式](https://docs.aws.amazon.com/ja_jp/lambda/latest/dg/urls-invocation.html#urls-response-payload)であるレスポンスから実際のHTTPレスポンスに変換するためのプロキシとなるコンテナです。

## 使い方

1. `docker build -t lambda-function-url-proxy .` でイメージをビルドしておく。
2. Lambda側のプロジェクト(docker-composeを使う想定)で設定を行う。
3. curl等で動作確認する。

### Lambda側のプロジェクトの設定

例えばaws-lambda-rieとawslambdaricを使う場合、以下のようにdocker-compose.ymlを書くと良いです。

```yml
services:
  lambda:
    ･･･
    ports:
      - :8080 # lambda側はポートをホストマシンに公開する必要は無い
  lambda-func-url-proxy:
    image: lambda-function-url-proxy
    ports:
      - 9000:80 # ホストマシン側にポートを公開
    environment:
      - LAMBDA_REQUEST_URL=http://lambda:8080/2015-03-31/functions/function/invocations # ホストはlambda, ポートはlambda側のポート
```

この場合、例えば

```sh
curl -i -XPOST 'http://localhost:9000/hoge' -d '{"hoge":"huga"}' -H 'Content-Type: application/json'
````

で動作確認出来ます。

## 注意

あくまでローカル環境用です。本番環境等では使わない方が良いでしょう。
