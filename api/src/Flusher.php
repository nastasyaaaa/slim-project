<?php

namespace App;

use Doctrine\ORM\EntityManagerInterface;

class Flusher implements IFlusher
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}