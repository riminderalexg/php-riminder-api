<?php

  require __DIR__ . '/../vendor/autoload.php';
  require __DIR__ . '/ReqExpHandler.php';
  require_once __DIR__ . '/RequestBodyUtils.php';
  /**
   *
   */
  class GuzzleWrapper
  {

    function __construct($options)
    {
      $options['http_errors'] = true;

      $this->guzzleClient = new GuzzleHttp\Client($options);
    }

    public function get($endpoint, $query=null)
    {
        $tmp = [];
        RequestBodyUtils::add_if_not_null($tmp, 'query', $query);
        $getLambda = function () use ($endpoint, $query) {
          return $this->guzzleClient->get($endpoint, ['query' => $query]);
        };
        return ReqExpHandler::exec($getLambda);
    }

    public function patch($endpoint, $bodyParams='')
    {
        $patchLambda = function () use ($endpoint, $bodyParams) {
          return $this->guzzleClient->patch($endpoint, ['json' => $bodyParams]);
        };
        return ReqExpHandler::exec($patchLambda);
    }

    public function post($endpoint, $bodyParams='')
    {
        $postLambda = function () use ($endpoint, $bodyParams) {
          return $this->guzzleClient->post($endpoint, ['json' => $bodyParams]);
        };
        return ReqExpHandler::exec($postLambda);
    }

    public function postFile($endpoint, $bodyParams, $file)
    {
        $multipart = [
          'multipart' => [
            [
              'name' => 'body',
              'contents' => json_encode($bodyParams),
            ],
            [
              'name' => 'file',
              'contents' => fopen($file, 'r'),
              'filename' => basename($file)
            ]
          ]
        ];
        $postLambda = function () use ($endpoint, $multipart) {
          return $this->guzzleClient->post($endpoint, $multipart);
        };
        return ReqExpHandler::exec($postLambda);
    }
  }

 ?>
