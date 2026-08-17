<?php

class Config
{
    private static array $cache = [];

    public static function get(string $key, ?string $file = null, bool $sections = true): object
    {
        if ($file) {
            self::$cache[$key] = new self($file, $sections);
        } elseif (!isset(self::$cache[$key])) {
            throw new RuntimeException("Config '{$key}' not found. Provide a file path or call get('{$key}', '/path/to/file.ini') first.");
        }
        return self::$cache[$key];
    }

    public static function forget(string $key): void
    {
        unset(self::$cache[$key]);
    }

    public static function has(string $key): bool
    {
        return isset(self::$cache[$key]);
    }

    private function __construct(private string $file, private bool $sections)
    {
        if (!file_exists($this->file)) {
            throw new RuntimeException("Config file not found: {$this->file}");
        }

        $parsed = parse_ini_file($this->file, $this->sections, INI_SCANNED_NONE);

        if ($parsed === false) {
            throw new RuntimeException("Error parsing config file: {$this->file}");
        }

        $this->data = $parsed;
    }

    private readonly array $data;

    public function toArray(): array
    {
        return $this->data;
    }

    public function toObject(): object
    {
        return (object) $this->data;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }
}

// Uso:
// $main = Config::get('main', ROOT . '/config/app.ini');
// $db = Config::get('database', ROOT . '/config/database.ini');
// $cache = Config::get('cache');
// $value = $main->get('debug');
// $all = $main->toArray();
