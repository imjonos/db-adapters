<?php

namespace Nos\DbAdapters;

use ClickHouseDB\Client;
use Nos\DbAdapters\Exceptions\SelectException;
use Nos\DbAdapters\Interfaces\DbClientAdapterInterface;

final class ClickHouseDbClientAdapter implements DbClientAdapterInterface
{
    private int $timeout = 10;
    private int $connectionTimeout = 5;
    private bool $ping = true;
    private ?Client $client = null;

    public function __construct(
        private readonly string $host,
        private readonly string $user,
        private readonly string $password,
        private readonly string $db,
        private readonly int $port = 8123
    ) {
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }

    /**
     * @return int
     */
    public function getConnectionTimeout(): int
    {
        return $this->connectionTimeout;
    }

    /**
     * @param int $connectionTimeout
     */
    public function setConnectionTimeout(int $connectionTimeout): void
    {
        $this->connectionTimeout = $connectionTimeout;
    }

    /**
     * @return bool
     */
    public function isPing(): bool
    {
        return $this->ping;
    }

    /**
     * @param bool $ping
     */
    public function setPing(bool $ping): void
    {
        $this->ping = $ping;
    }

    public function close(): void
    {
        $this->client = null;
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
        if (!$this->client) {
            $this->client = new Client([
                'host' => $this->host,
                'port' => $this->port,
                'username' => $this->user,
                'password' => $this->password
            ]);
            $this->client->database($this->db);
            $this->client->setTimeout($this->timeout);       // 10 seconds
            $this->client->setConnectTimeOut($this->connectionTimeout); // 5 seconds
            $this->client->ping($this->ping);
        }

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

    public function save(string $table, array $data, string $primaryKey = 'id'): int
    {
        // TODO: Implement save() method.
    }

    public function showTables(): array
    {
        return $this->getClient()->showTables();
    }
}
