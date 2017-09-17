<?php declare(strict_types=1);
namespace Uma\Infrastructure\Console;

use Doctrine\DBAL\Driver\PDOConnection;
use Exception;
use Illuminate\Console\Command as IlluminateCommand;
use Illuminate\Contracts\Config\Repository;
use RuntimeException;

/**
 * Pings database connections for up to a given timeout.
 *
 * @package Uma\Infrastructure\Console
 */
class PingDatabase extends IlluminateCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'uma:database:ping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pings database for up to 5 minutes';

    /**
     * Execute the command.
     *
     * @param Repository $config
     */
    public function handle(Repository $config)
    {
        $connection = $config->get('database.connections.uma', []);

        $host = $connection['host'];
        $database = $connection['database'];
        $username = $connection['username'];
        $password = $connection['password'];
        $charset = $connection['charset'];

        $endTimeout = microtime(true) + 300;

        while (microtime(true) < $endTimeout)
        {
            try
            {
                new PDOConnection("mysql:host=$host;dbname=$database;charset=$charset;", $username, $password);
                break;
            }
            catch (Exception $ignored)
            {
                sleep(1);
            }
        }

        if (microtime(true) >= $endTimeout)
        {
            throw new RuntimeException("Failed to ping database [$host=$database]");
        }
    }
}
