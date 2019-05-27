<?php

namespace ckuran;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Cache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\Tools\Setup;

final class DoctrineWrapper
{
    private $entityManager;
    private $connection;
    private $configuration = [];
    private $devMode = false;
    private $cache;
    private $proxyDir;

    public function __construct($configuration, $proxyDir, $devMode = false, Cache $cache = null)
    {
        # not supporting URLs yet
        if (false === is_array($configuration)) {
            throw new \InvalidArgumentException(sprintf("Configuration expected to be array %s given", gettype($configuration)));
        }

        if (false === empty(array_diff(array_keys($configuration), ['driver', 'host', 'user', 'password', 'dbname', 'charset', 'entities']))) {
            throw new \InvalidArgumentException(sprintf("Configuration is incomplete, missing critical fields"));
        }

        $this->configuration = $configuration;
        $this->devMode = $devMode;
        $this->cache = $cache;
        $this->proxyDir = $proxyDir;
    }

    /**
     * @return Connection
     * @throws DBALException
     */
    public function getConnection()
    {
        if (false === $this->connection instanceof Connection) {
            $this->connection = DriverManager::getConnection($this->configuration, new Configuration());
        }

        return $this->connection;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        if(false === $this->entityManager instanceof EntityManager) {
            $this->entityManager = EntityManager::create($this->configuration, $this->createSetup());
        }

        return $this->entityManager;
    }

    public function createSetup()
    {
        $setup = Setup::createAnnotationMetadataConfiguration([$this->configuration['entities']], $this->devMode, $this->proxyDir, $this->cache, false);
        $setup->setNamingStrategy(new UnderscoreNamingStrategy());

        return $setup;
    }

}
