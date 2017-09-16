<?php declare(strict_types=1);
namespace Uma;

use Doctrine\ORM\EntityManagerInterface;
use Laravel\Lumen\Testing\TestCase;
use Nelmio\Alice\Fixtures\Loader;

/**
 * Bootstraps the application and loads custom traits.
 *
 * @package Uma
 */
abstract class LumenTest extends TestCase
{
    /** @var Loader */
    protected $alice;
    /** @var EntityManagerInterface */
    protected $entityManager;

    /**
     * {@inheritdoc}
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';
        return $app;
    }

    /**
     * {@inheritdoc}
     */
    protected function setUpTraits()
    {
        parent::setUpTraits();
        $this->alice = new Loader('en_US');
        $this->entityManager = $this->app->make(EntityManagerInterface::class);

        $uses = array_flip(class_uses_recursive(get_class($this)));

        if (isset($uses[DatabaseTransactions::class]))
        {
            $this->beginDoctrineTransaction($this->entityManager);
        }
    }
}
