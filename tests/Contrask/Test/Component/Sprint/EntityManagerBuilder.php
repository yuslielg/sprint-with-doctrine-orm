<?php

namespace Contrask\Test\Component\Sprint;

use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\DefaultQuoteStrategy;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\ORM\Repository\DefaultRepositoryFactory;
use Doctrine\ORM\Tools\ResolveTargetEntityListener;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Configuration;

class EntityManagerBuilder
{
    /**
     * Creates entity manager
     *
     * @param array $mappings
     * @param array $entities
     * @param array $listeners
     * @param array $targetEntities
     * @return EntityManager
     */
    public function createEntityManager(
        $mappings = array(),
        $entities = array(),
        $listeners = array(),
        $targetEntities = array()
    )
    {
        $conn = array(
            'driver' => 'pdo_sqlite',
            'memory' => true,
        );

        $config = $this->createConfig($mappings);

        $evm = $this->createEventManager($listeners, $targetEntities);

        $em = EntityManager::create($conn, $config, $evm);

        $schemaTool = new SchemaTool($em);
        $schemaTool->dropSchema(array());
        $schemaTool->createSchema(
            array_map(
                function($class) use ($em) {
                    return $em->getClassMetadata($class);
                },
                $entities
            )
        );

        return $em;
    }

    /**
     * Creates configuration
     *
     * @param array $mappings
     * @return \Doctrine\ORM\Configuration
     */
    private function createConfig($mappings)
    {
        $config = new Configuration();
        $config->setProxyDir(sys_get_temp_dir());
        $config->setProxyNamespace('Proxy');
        $config->setAutoGenerateProxyClasses(true);
        $config->setClassMetadataFactoryName('Doctrine\\ORM\\Mapping\\ClassMetadataFactory');
        $config->setMetadataDriverImpl(new XmlDriver($mappings));
        $config->setDefaultRepositoryClassName('Doctrine\\ORM\\EntityRepository');
        $config->setQuoteStrategy(new DefaultQuoteStrategy());
        $config->setNamingStrategy(new DefaultNamingStrategy());
        $config->setRepositoryFactory(new DefaultRepositoryFactory());

        return $config;
    }

    /**
     * Creates entity manager
     *
     * @param array $listeners
     * @param array $targetEntities
     * @return EventManager
     */
    private function createEventManager($listeners, $targetEntities)
    {
        $evm = new EventManager;
        foreach ($listeners as $listener) {
            $evm->addEventSubscriber($listener);
        }

        $listener = new ResolveTargetEntityListener;
        foreach ($targetEntities as $original => $new) {
            $listener->addResolveTargetEntity($original, $new, array());
        }
        $evm->addEventListener(Events::loadClassMetadata, $listener);

        return $evm;
    }
}
