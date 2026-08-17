<?php

class SFTP
{
    private \ssh2_resource $connection;
    private ?\stream $stream = null;
    private string $currentDir = '/';

    public function __construct(
        private string $configName,
        private array $params = [],
        ?string $sftpConfigPath = null
    ) {
        $configFile = $sftpConfigPath ?? (defined('SFTP') ? SFTP : '');

        if (!file_exists($configFile)) {
            throw new RuntimeException("SFTP config file not found: {$configFile}");
        }

        $config = parse_ini_file($configFile, true);

        if (!isset($config[$configName])) {
            throw new RuntimeException("SFTP config '{$configName}' not found in {$configFile}");
        }

        $settings = $config[$configName];

        $this->connection = ssh2_connect(
            $settings['host'],
            (int) ($settings['port'] ?? 22),
            $this->params
        );

        if (!$this->connection) {
            throw new RuntimeException("Cannot connect to {$settings['host']}");
        }

        $auth = ssh2_auth_password(
            $this->connection,
            $settings['username'],
            $settings['password']
        );

        if (!$auth) {
            throw new RuntimeException("Authentication failed for {$settings['username']}");
        }
    }

    public function exec(string $command): string
    {
        $stream = ssh2_exec($this->connection, $command);

        if (!$stream) {
            throw new RuntimeException("Failed to execute: {$command}");
        }

        stream_set_blocking($stream, true);
        $output = '';

        while ($buffer = fread($stream, 4096)) {
            $output .= $buffer;
        }

        fclose($stream);
        return trim($output);
    }

    public function listDir(string $path = ''): array
    {
        $dir = $path ? $this->resolvePath($path) : $this->currentDir;
        $output = $this->exec("ls -la {$dir}");
        return $this->parseListings($output);
    }

    public function upload(string $localPath, string $remotePath): bool
    {
        $sftp = ssh2_sftp($this->connection);
        $remote = "ssh://{$sftp}{$remotePath}";
        return copy($localPath, $remote);
    }

    public function download(string $remotePath, string $localPath): bool
    {
        $sftp = ssh2_sftp($this->connection);
        $remote = "ssh://{$sftp}{$remotePath}";
        return copy($remote, $localPath);
    }

    public function currentDir(): string
    {
        return $this->currentDir;
    }

    public function cd(string $path): self
    {
        $this->exec("cd {$path}");
        $this->currentDir = $path;
        return $this;
    }

    public function close(): void
    {
        if (is_resource($this->connection)) {
            fclose($this->connection);
        }
    }

    private function resolvePath(string $path): string
    {
        return str_starts_with($path, '/') ? $path : $this->currentDir . '/' . $path;
    }

    private function parseListings(string $output): array
    {
        $files = [];
        $lines = explode("\n", $output);

        foreach ($lines as $line) {
            if (preg_match('#^([-dlprwxs]+)\s+\d+\s+\S+\s+\S+\s+(\d+)\s+(\w+\s+\d+\s+\d+:\d+)\s+(\S+)$#', $line, $matches)) {
                $files[] = [
                    'perms' => $matches[1],
                    'size' => (int) $matches[2],
                    'date' => $matches[3],
                    'name' => $matches[4],
                ];
            }
        }

        return $files;
    }
}

// Uso:
// $sftp = new SFTP('production');
// $sftp->cd('/var/www');
// $sftp->upload('/local/file.css', '/var/www/file.css');
// $sftp->download('/var/www/file.css', '/local/file.css');
// $files = $sftp->listDir();
