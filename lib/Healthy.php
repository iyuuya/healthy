<?php

class Healthy {
  private $applications;
  private $timeout;

  function __construct($applications, $timeout = 60) {
    $this->applications = $applications;
    $this->timeout = $timeout;
  }

  public function getStatuses() {
    return array_map(function ($app) {
      return $this->getStatus("http://localhost/{$app}/");
    }, $this->applications);
  }

  private function getStatus($url) {
    $header = null;
    $options = [
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_RETURNTRANSFER => false,
      CURLOPT_HEADER => true,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_ENCODING => '',
      CURLOPT_USERAGENT => 'healthy',
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_AUTOREFERER => true,
      CURLOPT_CONNECTTIMEOUT => $this->timeout,
      CURLOPT_TIMEOUT => $this->timeout,
      CURLOPT_MAXREDIRS => 5,
    ];

    $channel = curl_init($url);
    curl_setopt_array($channel, $options);
    curl_exec($channel);
    if (!curl_errno($channel)) {
      $header = curl_getinfo($channel);
    }
    curl_close($channel);
    return $header['http_code'];
  }
}
