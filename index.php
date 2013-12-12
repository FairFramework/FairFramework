<?php

require_once 'bootstrap.php';
$sum = 0;
$startTime = microtime(true);

$context = new Ff\Lib\Context(
    $_GET,
    $_POST,
    $_COOKIE,
    $_SESSION,
    $_SERVER
);

$configuration = new Ff\Lib\Configuration();
$configuration->load();

$bus = new Ff\Lib\Bus($configuration, $context);

$application = new Ff\Lib\Application($bus);

$application->start();

$executionTime = microtime(true) - $startTime;
echo '
<ul class="nav nav-pills nav-stacked">
  <li class="active">
    <a href="#">
      <span class="badge pull-right">'.$executionTime.'</span>
      Execution Time
    </a>
  </li>
</ul>
';