<?php

class Autoload
{
    private array $dirs = [];

    public function addPath(string $dir): self
    {
        if (is_dir($dir)) {
            $this->dirs[] = rtrim($dir, DIRECTORY_SEPARATOR);
        }
        return $this;
    }

    public function register(): self
    {
        spl_autoload_register($this, true, true);
        return $this;
    }

    public function autoload(string $class): void
    {
        $file = str_replace(['_', '\\'], DIRECTORY_SEPARATOR, $class) . '.php';

        foreach ($this->dirs as $dir) {
            $fullPath = $dir . DIRECTORY_SEPARATOR . $file;
            if (file_exists($fullPath)) {
                require $fullPath;
                return;
            }
        }
    }
}

// Uso:
// $loader = new Autoload();
// $loader->addPath(ROOT . '/app')
//        ->addPath(ROOT . '/src')
//        ->register();
