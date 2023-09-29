<?php

namespace Nos\DbAdapters\Interfaces;

interface DbClientAdapterInterface
{
    public function getClient(): mixed;

    public function selectAll(string $query, array $bindings = []): array;

    public function selectOne(string $query, array $bindings = []): ?array;

    public function exec(string $query): void;

    public function save(string $table, array $data, string $primaryKey = 'id'): int;

    public function showTables(): array;

    public function close(): void;
}
