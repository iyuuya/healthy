<?php

namespace Healthy;

// FIXME: カオス
class Generator {
  private $path;
  private $applications;

  function __construct($path, $applications) {
    $this->path = $path;
    $this->applications = $applications;
  }

  public function call() {
    $this->copyLib();
    $this->generateHtAccess();
    $this->generateRootIndex();
    $this->generateChildIndexes();
  }

  private function copyLib() {
    if (!is_dir("{$this->path}/lib")) {
      mkdir("{$this->path}/lib", 0755, false);
    }
    if (!copy('./lib/Healthy.php', "{$this->path}/lib/Healthy.php")) {
      fputs(STDERR, "failed copy to {$this->path}/lib/Healthy.php");
    }
  }

  private function generateHtAccess() {
    $filename = "{$this->path}/lib/.htaccess";
    return file_put_contents($filename, "Deny from all\n") && chmod($filename, 0600);
  }

  private function generateRootIndex() {
    $app_str = "'" . join("','", $this->applications) . "'";
    $contents = <<<"CONTENTS"
<?php
require_once(realpath(dirname(__FILE__) . '/lib/Healthy.php'));
\$healthy = new Healthy([$app_str]);
\$healthy->run();

CONTENTS;
    return file_put_contents("{$this->path}/index.php", $contents);
  }

  private function generateChildIndex($application) {
    $app_path = "{$this->path}/${application}";
    if (!is_dir($app_path)) {
      mkdir($app_path, 0755, false);
    }
    $contents = <<<"CONTENTS"
<?php
require_once(realpath(dirname(__FILE__) . '/../lib/Healthy.php'));
\$healthy = new Healthy(['$application']);
\$healthy->run();

CONTENTS;
    return file_put_contents("{$app_path}/index.php", $contents);
  }

  private function generateChildIndexes() {
    foreach ($this->applications as $application) {
      $this->generateChildIndex($application);
    }
  }
}
