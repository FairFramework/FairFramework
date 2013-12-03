<?php

require_once './code/Ff/Autoload.php';

spl_autoload_register('Ff\\Autoload::load');

$root = __DIR__;

define('ROOT_DIR', $root);

Ff\Autoload::addIncludePath(array(
    $root . '/code',
    $root . '/runtime',
    $root . '/interface',
    $root
));

stream_register_wrapper('ff.template', 'Ff\Lib\Render\Template\Stream');

$startTime = microtime(true);

$configuration = new Ff\Lib\Configuration();
$configuration->load();

$bus = new Ff\Lib\Bus($configuration);

$application = new Ff\Lib\Application($bus);

$context = new Ff\Lib\Context(
    $_GET,
    $_POST,
    $_COOKIE,
    $_SESSION,
    $_SERVER
);

$application->start($context);

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