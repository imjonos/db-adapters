# DB Adapters

Base database adapters

## Installation

Via Composer

``` bash
$ composer require imjonos/db-adapters
```

## How to use

Supported 2 connection types: <br>
PDO - \Nos\DbAdapters\MysqlDbClientAdapter::class <br>
HTTP (smi2/phpclickhouse) - \Nos\DbAdapters\ClickHouseDbClientAdapter::class <br>

``` php
interface DbClientAdapterInterface
{
    public function getClient(): mixed;

    public function selectAll(string $query, array $bindings = []): array;

    public function selectOne(string $query, array $bindings = []): ?array;

    public function exec(string $query): void;

    public function save(string $table, array $data): int;

    public function showTables(): array;

    public function close(): void;
}
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## License

license. Please see the [license file](license.md) for more information.
