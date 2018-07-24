<?php

  require_once __DIR__ . '/ReqExpHandler.php';
  require_once __DIR__ . '/RequestBodyUtils.php';
  /**
   *
   */
  class GuzzleWrapper
  {

    function __construct($options, $base_url)
    {
      $options['http_errors'] = true;

      $this->guzzleClient = new GuzzleHttp\Client($options);
      $this->base_url = $base_url;
    }

    // Usage of lambda to execute the call, to handle transfert/ response
    // errors easily see ReqExpHandler::exec
    public function get($endpoint, $query=null)
    {
        $tmp = [];
        $endpoint = $this->base_url.$endpoint;
        RequestBodyUtils::add_if_not_null($tmp, 'query', $query);
        $getLambda = function () use ($endpoint, $query) {
          return $this->guzzleClient->get($endpoint, ['query' => $query]);
        };
        return ReqExpHandler::exec($getLambda);
    }

    public function patch($endpoint, $bodyParams='')
    {
        $endpoint = $this->base_url.$endpoint;
        $patchLambda = function () use ($endpoint, $bodyParams) {
          return $this->guzzleClient->patch($endpoint, ['json' => $bodyParams]);
        };
        return ReqExpHandler::exec($patchLambda);
    }

    public function post($endpoint, $bodyParams='')
    {
        $endpoint = $this->base_url.$endpoint;
        $postLambda = function () use ($endpoint, $bodyParams) {
          return $this->guzzleClient->post($endpoint, ['json' => $bodyParams]);
        };
        return ReqExpHandler::exec($postLambda);
    }

    public function postFile($endpoint, $bodyParams, $file)
    {
        $endpoint = $this->base_url.$endpoint;
        $multipart = [
          'multipart' => [
            [
              'name' => 'file',
              'contents' => fopen($file, 'r'),
              'filename' => basename($file)
            ]
          ]
        ];

        // Add other datas as multipart field.
        foreach ($bodyParams as $key => $value) {
          $contents = $value;
          if (is_array($value)) {
            $contents = json_encode($value);
          }
          $multipart['multipart'][] = [
            'name' => $key,
            'contents' => $contents
          ];
        }
        
        $postLambda = function () use ($endpoint, $multipart) {
          return $this->guzzleClient->post($endpoint, $multipart);
        };
        return ReqExpHandler::exec($postLambda);
    }
  }

 ?>
