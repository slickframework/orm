<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Slick\Http\Message\Server\Request;
use Slick\WebStack\Infrastructure\FrontController\WebApplication;

$startTime = microtime(true);

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

// ------------------------------------------------------
//  Initialize application
// ------------------------------------------------------
$request = new Request();
$application = new WebApplication($request, dirname(__DIR__));

// ------------------------------------------------------
//  Load any bootstrap actions
// ------------------------------------------------------
$bootstrapFile = APP_ROOT . '/config/bootstrap.php';
if (is_file($bootstrapFile)) {
    include_once $bootstrapFile;
}

// ------------------------------------------------------
//  Run application and output the response.
// ------------------------------------------------------
$application->output($application->run());

/** @var \Psr\Log\LoggerInterface $logger */
$logger = \Slick\WebStack\Infrastructure\DependencyContainerFactory::instance()->container()->get('default.logger');
$endTime = microtime(true) - $startTime;
$logger->info("It's running.", ['execution_time' => $endTime]);
