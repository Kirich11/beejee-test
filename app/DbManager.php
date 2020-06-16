<?php

namespace App;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Setup;

/**
 * Database manager that provides Doctrine EntityManager and Doctrine EntityRepository
 */
class DbManager
{
    private static $instance = null;
    private $manager;

    private function __construct()
    {
        $isDevMode = true;
        $proxyDir = null;
        $cache = null;
        $useSimpleAnnotationReader = false;
        $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/Models"), $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);
        $connection = ['url' => env('DATABASE_URL')];

        $this->manager = EntityManager::create($connection, $config);
    }
    
    /**
     * load DbManager instance
     */
    public static function getInstance() : self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * load EntityManager from instance
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * static call for getManager
     */
    public static function createManager()
    {
        return self::getInstance()->getManager();
    }

    /**
     * load EntityRepository by classname
     */
    public static function getRepository(string $classname) : EntityRepository
    {
        return self::getInstance()->getManager()->getRepository($classname);
    }

    private function __clone()
    {
    }
    private function __wakeup()
    {
    }
}
