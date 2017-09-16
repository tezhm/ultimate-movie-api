<?php declare(strict_types=1);
namespace Uma;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Used for wrapping test cases in database transactions.
 *
 * @package Uma
 */
trait DatabaseTransactions
{
    /**
     * Starts a transaction which rolls back on test completion.
     *
     * @param EntityManagerInterface $entityManager
     */
    protected function beginDoctrineTransaction(EntityManagerInterface $entityManager)
    {
        $entityManager->beginTransaction();

        $this->beforeApplicationDestroyed(function () use ($entityManager)
        {
            $entityManager->rollback();
        });
    }
}
