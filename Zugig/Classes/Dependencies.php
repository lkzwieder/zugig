<?php

class Dependencies
{
    private array $queue = [];
    private array $executed = [];
    private int $nn = 0;
    private array $data = [];

    public function add(mixed $content, ?string $name = null, array $deps = []): self
    {
        $this->addCode($content, $name, $deps);
        return $this;
    }

    public function addData(mixed $content, ?string $name = null, array $deps = []): self
    {
        return $this->add($content, $name, $deps);
    }

    public function addFile(string $file, ?string $name = null, array $deps = []): self
    {
        $this->add(['file', $file], $name, $deps);
        return $this;
    }

    public function addCode(mixed $content, ?string $name = null, array $deps = []): self
    {
        $this->add(['code', $content], $name, $deps);
        return $this;
    }

    public function get(): string
    {
        $this->resolve();
        return $this->flatten();
    }

    private function add(mixed $content, ?string $name, array $deps): void
    {
        if (!empty($deps)) {
            $remaining = $this->pendingDeps($deps);
            if (!empty($remaining)) {
                $this->enqueue($content, $name ?? '_lkz' . $this->nn++, $remaining);
                return;
            }
        }
        $this->insert($content, $name);
    }

    private function pendingDeps(array $deps): array
    {
        $pending = [];
        foreach ($deps as $dep) {
            if (!in_array($dep, $this->executed, true)) {
                $pending[] = $dep;
            }
        }
        return $pending;
    }

    private function enqueue(mixed $content, string $name, array $deps): void
    {
        if (!isset($this->queue[$name])) {
            $this->queue[$name] = ['content' => $content, 'deps' => $deps];
        }
    }

    private function insert(mixed $content, ?string $name): void
    {
        $this->data[] = $content;
        if ($name !== null && !str_starts_with($name, '_lkz')) {
            $this->markExecuted($name);
        }
    }

    private function markExecuted(string ...$names): void
    {
        foreach ($names as $name) {
            if (!in_array($name, $this->executed, true)) {
                $this->executed[] = $name;
                $this->resolveDep($name);
            }
        }
    }

    private function resolveDep(string $name): void
    {
        foreach ($this->queue as $queueName => $item) {
            if (in_array($name, $item['deps'], true)) {
                $item['deps'] = array_filter($item['deps'], fn($d) => $d !== $name);
                if (empty($item['deps'])) {
                    unset($this->queue[$queueName]);
                    $this->insert($item['content'], $queueName);
                }
            }
        }
    }

    private function resolve(): void
    {
        $changed = true;
        while ($changed && !empty($this->queue)) {
            $changed = false;
            foreach ($this->queue as $name => $item) {
                if (empty($item['deps'])) {
                    $this->insert($item['content'], $name);
                    unset($this->queue[$name]);
                    $changed = true;
                    break;
                }
            }
        }
    }

    private function flatten(): string
    {
        $result = '';
        foreach ($this->data as $item) {
            if (is_array($item) && $item[0] === 'code') {
                $result .= strip_tags($item[1]);
            } elseif (is_array($item) && $item[0] === 'file') {
                $file = str_starts_with($item[1], 'http')
                    ? $item[1]
                    : APP_ROOT . DIRECTORY_SEPARATOR . $item[1];
                $result .= file_get_contents($file);
            } else {
                $result .= $item;
            }
        }
        return trim($result);
    }
}

// Uso:
// $deps = new Dependencies();
// $deps->addFile('css/base.css', 'base')
//      ->addFile('css/layout.css', 'layout', ['base'])
//      ->addCode('<style>body{margin:0}</style>', 'inline')
//      ->addFile('js/main.js', 'main', ['layout'])
//      ->get();
