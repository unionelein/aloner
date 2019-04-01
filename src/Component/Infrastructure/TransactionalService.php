<?php

namespace App\Component\Infrastructure;

use Doctrine\ORM\EntityManagerInterface;

class TransactionalService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function execute(callable $operation): void
    {
        $this->em->beginTransaction();

        try {
            $operation($this->em);

            $this->em->flush();
            $this->em->commit();
        } catch (\Exception $e) {
            $this->em->rollback();

            throw $e;
        }
    }
}