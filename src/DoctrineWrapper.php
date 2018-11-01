<?php

namespace IFX;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Setup;

class DoctrineWrapper
{
    protected static $dbal = [];
    protected static $orm = [];

    /**
     * @param string $prefix
     *
     * @return Connection
     * @throws DBALException
     */
    public static function getConnection($prefix = 'default')
    {
        $credentials = self::getCredentials($prefix);

        if (!in_array($prefix, self::$dbal)) {
            $config = new Configuration();
            self::$dbal[$prefix] = DriverManager::getConnection($credentials, $config);
        }

        return self::$dbal[$prefix];
    }

    /**
     * @param string $prefix
     *
     * @return EntityManager
     * @throws ORMException
     */
    public static function getEntityManager($prefix = 'default')
    {
        $credentials = self::getCredentials($prefix);

        if (!in_array($prefix, self::$orm)) {
            $setup = Setup::createAnnotationMetadataConfiguration([$credentials['entities']], getenv('DEV_MODE'), getenv('PROXY_DIR'), null, false);
            self::$orm[$prefix] = EntityManager::create($credentials, $setup);
        }

        return self::$orm[$prefix];
    }

    /**
     * @param $prefix
     *
     * @return array
     */
    private static function getCredentials($prefix)
    {
        $config = [
            'driver'   => getenv($prefix . '_DB_DRIVER'),
            'host'     => getenv($prefix . '_DB_HOST'),
            'user'     => getenv($prefix . '_DB_USER'),
            'password' => getenv($prefix . '_DB_PASSWORD'),
            'dbname'   => getenv($prefix . '_DB_NAME'),
            'entities' => getenv($prefix . '_ENTITY_DIR')
        ];

        return $config;
    }
}
