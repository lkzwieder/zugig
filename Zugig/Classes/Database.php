<?php

class Database
{
    private static array $instances = [];

    public static function connection(string $name): PDO
    {
        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = self::makeConnection($name);
        }
        return self::$instances[$name];
    }

    public static function close(string $name): void
    {
        self::$instances[$name] = null;
    }

    public static function clearAll(): void
    {
        self::$instances = [];
    }

    private static function makeConnection(string $name): PDO
    {
        $settings = parse_ini_file(DATABASES, true);

        if (!isset($settings[$name])) {
            throw new RuntimeException("Database config '{$name}' not found in " . DATABASES);
        }

        $config = $settings[$name];

        $dns = match ($config['driver'] ?? '') {
            'dblib' => "dblib:host={$config['host']}:{$config['port']};dbname={$config['dbname']}",
            'mysql' => "mysql:host={$config['host']};port={$config['port'] ?? 3306};dbname={$config['dbname']}",
            'sqlite' => "sqlite:{$config['dbname']}",
            default => throw new RuntimeException("Unsupported driver: {$config['driver']}"),
        };

        $pdo = new PDO($dns, $config['username'] ?? '', $config['password'] ?? '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $pdo;
    }

    public static function bulkInsert(string $table, array $fields, array $data, bool $replace = false, int $batchSize = 1000): array
    {
        $columns = static::columns($fields);
        $queries = [];
        $total = count($data);

        for ($i = 0; $i < $total; $i += $batchSize) {
            $batch = array_slice($data, $i, $batchSize);
            $values = array_map(fn($row) => static::values(array_map('json_encode', $row)), $batch);
            $queries[] = sprintf(
                '%s INTO %s %s VALUES %s',
                $replace ? 'REPLACE' : 'INSERT',
                $table,
                $columns,
                implode(', ', $values)
            );
        }

        return $queries;
    }

    public static function bulkUpdate(string $query, array $data, int $batchSize = 3000): array
    {
        $queries = [];
        $total = count($data);

        for ($i = 0; $i < $total; $i += $batchSize) {
            $batch = array_slice($data, $i, $batchSize);
            $queries[] = sprintf($query, static::values($batch));
        }

        return $queries;
    }

    private static function columns(array $fields): string
    {
        return '(' . implode(', ', array_map(fn($f) => "`{$f}`", $fields)) . ')';
    }

    private static function values(array $data): string
    {
        return '(' . implode(', ', $data) . ')';
    }
}

// Uso:
// $pdo = Database::connection('default');
// $stmt = $pdo->query('SELECT * FROM users')->fetchAll();
//
// $queries = Database::bulkInsert('users', ['name', 'email'], [
//     ['John', 'john@example.com'],
//     ['Jane', 'jane@example.com'],
// ], false, 1000);
