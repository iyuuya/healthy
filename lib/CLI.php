<?php

namespace Healthy;

require_once(dirname(__FILE__) . '/Generator.php');

class CLI {
  private $options;

  function __construct() {
    $shortopts = 'h';
    $longopts[] = 'help';
    $longopts[] = 'path:';
    $longopts[] = 'application:';
    $this->options = getopt($shortopts, $longopts);
  }

  public function start() {
    $this->checkOptions();
    $this->process();
  }

  private function process() {
    $path = $this->options['path'];
    if (!is_dir($path)) {
      fputs(STDERR, "{$path} directory not found\n");
      exit(1);
    }

    $applications = null;
    if (is_string($this->options['application'])) {
      $applications[] = $this->options['application'];
    } else {
      $applications = $this->options['application'];
    }

    $generator = new Generator($path, $applications);
    $generator->call();
  }

  private function checkOptions() {
    if ($this->options === false) {
      fputs(STDERR, "Thre was a problem reading in the options.\n", print_r($argv, true));
      exit(1);
    }

    if (isset($this->options['h']) || isset($this->options['help'])) {
      usage();
    } else if (!isset($this->options['path'])) {
      usage();
    } else if (!isset($this->options['application'])) {
      usage();
    }
  }

  private function usage() {
    $usage =<<<'USAGE'
Usage: healthy [options]
  -h : --help         show this
       --path         generate path [require]
                      (NOT create at the directory in this command)
       --application  application name(s) [require]

  Example:
    healthy --path /path/to/healthcheck --application foo --application bar --application baz
USAGE;
    echo $usage;
    exit(0);
  }
}
