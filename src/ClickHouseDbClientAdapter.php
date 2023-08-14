<?php

namespace Nos\DbAdapters;

use ClickHouseDB\Client;
use Nos\DbAdapters\Exceptions\SelectException;
use Nos\DbAdapters\Interfaces\DbClientAdapterInterface;

final class ClickHouseDbClientAdapter implements DbClientAdapterInterface
{
    private const TIMEOUT = 10;
    private const CONNECTION_TIMEOUT = 5;
    private const PING = true;
    private Client $client;

    public function __construct(string $host, string $user, string $password, string $db, int $port = 8123)
    {
        $this->client = new Client([
            'host' => $host,
            'port' => $port,
            'username' => $user,
            'password' => $password
        ]);
        $this->client->database($db);
        $this->client->setTimeout(self::TIMEOUT);       // 10 seconds
        $this->client->setConnectTimeOut(self::CONNECTION_TIMEOUT); // 5 seconds
        $this->client->ping(self::PING);
    }

    public function close(): void
    {
        // TODO: Implement close() method.
    }

    /**
     * @throws SelectException
     */
    public function selectAll(string $query, array $bindings = []): array
    {
        $statement = $this->getClient()
            ->select($query, $bindings);

        if ($statement->error()) {
            throw new SelectException('Can\'t select!');
        }

        return $statement->rows();
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @throws SelectException
     */
    public function selectOne(string $query, array $bindings = []): ?array
    {
        $statement = $this->getClient()
            ->select($query, $bindings);

        if ($statement->error()) {
            throw new SelectException('Can\'t select.');
        }

        return $statement->fetchRow();
    }

    public function exec(string $query): void
    {
        $this->getClient()->write($query);
    }

    public function save(string $table, array $data): int
    {
        // TODO: Implement save() method.
    }

    public function showTables(): array
    {
        return $this->getClient()->showTables();
    }
}
