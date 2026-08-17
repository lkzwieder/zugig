<?php

class Glue
{
    private array $items = [];

    public function add(mixed $content, string $type = 'code', ?string $name = null, array $deps = []): self
    {
        $this->items[] = [
            'content' => $content,
            'type' => $type,
            'name' => $name,
            'deps' => $deps,
        ];
        return $this;
    }

    public function addCode(mixed $content, ?string $name = null, array $deps = []): self
    {
        return $this->add($content, 'code', $name, $deps);
    }

    public function addFile(string $file, ?string $name = null, array $deps = []): self
    {
        return $this->add($file, 'file', $name, $deps);
    }

    public function flush(): string
    {
        $result = '';

        foreach ($this->items as $item) {
            if ($item['type'] === 'code') {
                $result .= strip_tags($item['content']);
            } elseif ($item['type'] === 'file') {
                $file = str_starts_with($item['content'], 'http')
                    ? $item['content']
                    : APP_ROOT . DIRECTORY_SEPARATOR . $item['content'];
                $result .= file_get_contents($file);
            }
        }

        return trim($result);
    }
}

// Uso:
// $glue = new Glue();
// $glue->addFile('css/base.css')
//      ->addFile('css/layout.css', deps: ['base'])
//      ->addCode('<style>body{margin:0}</style>')
//      ->flush();
