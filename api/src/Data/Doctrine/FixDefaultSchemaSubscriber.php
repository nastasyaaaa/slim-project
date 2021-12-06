<?php

namespace App\Data\Doctrine;

use Doctrine\ORM\Tools\ToolEvents;
use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Schema\PostgreSqlSchemaManager;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;

class FixDefaultSchemaSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            ToolEvents::postGenerateSchema => 'postGenerateSchema',
        ];
    }

    public function postGenerateSchema(GenerateSchemaEventArgs $args): void
    {
        $schemaManager = $args
            ->getEntityManager()
            ->getConnection()
            ->getSchemaManager();

        if (!$schemaManager instanceof PostgreSqlSchemaManager) {
            return;
        }

        foreach ($schemaManager->getExistingSchemaSearchPaths() as $namespace) {
            if (!$args->getSchema()->hasNamespace($namespace)) {
                $args->getSchema()->createNamespace($namespace);
            }
        }
    }
}