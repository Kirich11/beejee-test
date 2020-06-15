<?php
/**
 * Doctrine console runner for migrations
 */

require_once "vendor/autoload.php";

use App\DbManager;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');
$manager = DbManager::getInstance()->getManager();

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($manager);