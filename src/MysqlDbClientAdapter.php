<?php

namespace Nos\DbAdapters;

use Nos\DbAdapters\Interfaces\DbClientAdapterInterface;
use PDO;

final class MysqlDbClientAdapter implements DbClientAdapterInterface
{
    private ?PDO $client = null;

    public function __construct(
        private readonly string $host,
        private readonly string $user,
        private readonly string $password,
        private readonly string $db,
        private readonly int $port = 3306
    ) {
    }

    public function close(): void
    {
        $this->client = null;
    }

    public function selectOne(string $query, array $bindings = []): ?array
    {
        $result = $this->getClient()->prepare($query);
        $result->execute($bindings);

        return $result->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getClient(): PDO
    {
        if ($this->client === null) {
            $dsn = 'mysql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->db . ';charset=utf8';
            $this->client = new PDO($dsn, $this->user, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        }

        return $this->client;
    }

    public function exec(string $query): void
    {
        $this->getClient()->exec($query);
    }

    public function save(string $table, array $data): int
    {
        $columnNames = array_keys($data);
        $columns = implode(',', $columnNames);
        $values = implode(',', array_map(fn($item) => ':' . $item, $columnNames));
        $sql = sprintf('REPLACE INTO %s (%s) VALUES (%s)',$table, $columns,  $values);
           
        $this->getClient()
            ->prepare($sql)
            ->execute($data);
        
        return (int) $this->getClient()
            ->lastInsertId();
    }

    public function showTables(): array
    {
        return $this->selectAll('SHOW TABLES');
    }

    public function selectAll(string $query, array $bindings = []): array
    {
        $result = $this->getClient()->prepare($query);
        $result->execute($bindings);

        return $result->fetchAll(PDO::FETCH_DEFAULT) ?: [];
    }
}
