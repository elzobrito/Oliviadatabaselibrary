<?php

namespace OliviaDatabaseLibrary;
use PDO;

interface ConnectionInterface
{
    public function connect(): PDO;
    public function executeInsert(string $sql, array $values): ?string;
    public function executeSelect(string $sql, array $values): ?array;
    public function close(): void;
}
