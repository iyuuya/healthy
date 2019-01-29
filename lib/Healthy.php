<?php

class Healthy {
  private $applications;
  private $timeout;

  function __construct($applications, $timeout = 60) {
    $this->applications = $applications;
    $this->timeout = $timeout;
  }

  public function run() {
    if (count($this->applications) === 1) {
      $this->runAppForChild();
    } else {
      $this->runAppForAll();
    }
  }

  public function runAppForAll() {
    $statuses = $this->getStatuses();
    $filtered = array_filter($statuses, function ($var) {
      return $var === 200;
    });
    if (empty($filtered)) {
      $this->render_ng(504);
    } else if (count($statuses) === count($filtered)) {
      $this->render_ok();
    } else {
      $this->render_ng(max($statuses));
    }
  }

  public function runAppForChild() {
    $status = $this->getStatuses()[0];

    if ($status === 200) {
      $this->render_ok();
    } else {
      $this->render_ng($status);
    }
  }

  private function getStatuses() {
    return array_map(function ($app) {
      return $this->getStatus("http://localhost/{$app}/");
    }, $this->applications);
  }

  private function render_ok() {
    echo "200\n";
  }

  private function render_ng($status) {
    http_response_code($status);
    echo "{$status}\n";
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
    } else {
      $header = [
        'http_code' => 504
      ];
    }
    curl_close($channel);
    return $header['http_code'];
  }
}
