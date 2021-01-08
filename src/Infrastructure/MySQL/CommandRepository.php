<?php

namespace App\Infrastructure\MySQL;

use Doctrine\DBAL\Driver\Connection;
use App\Domain\Command;

class CommandRepository
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function inject(Command $command, int $count)
    {
        $result = $this->connection->query('SELECT MAX(command_id) AS max FROM command');
        $i = ((int) $result->fetch()['max']) + 1;
        $maxId = $i + $count;

        $query = 'INSERT INTO command ' .
            '(command_id, command_name, command_line, command_type) ' .
            'VALUES ';

        $name = $command->getName() . '_';
        $line = $command->getLine();
        $type = $command->getType();
        for ($i; $i < $maxId; $i++) {
            $query .= '(' .
                $i . ',' .
                '"' . $name . $i . '",' .
                '"' . $line . '",' .
                $type .
                '),';
        }
        $query = rtrim($query, ',');

        $this->connection->query($query);
    }

    public function purge()
    {
        $this->connection->query('TRUNCATE command');
    }
}