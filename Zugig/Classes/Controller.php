<?php

class Controller
{
    protected array $vars = [];
    protected string $viewPath = '';
    protected string $basePath = '';

    public function __construct(string $basePath = APP_ROOT)
    {
        $this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR);
    }

    public function setVar(string $key, mixed $value): self
    {
        $this->vars[$key] = $value;
        return $this;
    }

    public function setVars(array $data): self
    {
        $this->vars = array_merge($this->vars, $data);
        return $this;
    }

    public function render(string $view = '', array $overrides = []): void
    {
        $view = $view ?: $this->inferView();
        $data = array_merge($this->vars, $overrides);
        $viewFile = $this->viewPath . DIRECTORY_SEPARATOR . $view . '.php';

        if (!file_exists($viewFile)) {
            throw new RuntimeException("View not found: {$viewFile}");
        }

        extract($data, EXTR_SKIP);
        require $viewFile;
    }

    public function redirect(string $url, int $code = 302): void
    {
        header("Location: {$url}", true, $code);
        exit;
    }

    protected function viewDir(): string
    {
        $class = static::class;
        $parts = explode('\\', $class);
        $last = end($parts);
        return $this->basePath . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . strtolower($last);
    }

    protected function inferView(): string
    {
        $class = static::class;
        $parts = explode('\\', $class);
        $last = end($parts);
        return strtolower(str_replace('Controller', '', $last));
    }
}

// Uso:
// class UsersController extends Controller {
//     public function show() {
//         $user = User::find(1);
//         $this->setVar('user', $user);
//         $this->render('show');
//     }
//     public function delete() {
//         $this->redirect('/users');
//     }
// }
