<?php
declare(strict_types=1);

use App\Domain\Documents\DocumentsRepository;
use App\Infrastructure\Persistence\Documents\InMemoryDocumentsRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our DocumentsRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        DocumentsRepository::class => \DI\autowire(InMemoryDocumentsRepository::class),
    ]);

};
