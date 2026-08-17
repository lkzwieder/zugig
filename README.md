# zugig

High performance PHP framework, made with "keep it simple" idea in mind.

**PHP 8.0+** — Minimalist, fast, dependency-free.

## Core Classes

- `Router` — HTTP router with method matching & named capture groups
- `Autoload` — Simple file-based autoloader
- `Controller` — MVC controller with view rendering
- `Database` — PDO wrapper with bulk operations
- `Curl` — HTTP client with parallel requests
- `Config` — INI config loader
- `Dependencies` — JS/CSS dependency ordering
- `Glue` / `GlueCSS` / `GlueJS` — Asset bundling
- `MinifierCSS` — CSS minification
- `Memcached` — Cache wrapper
- `SFTP` — SSH/SFTP client
- `Packer` — Asset file packing & caching
- `TestController` — Simple assertions

## Setup

```php
// bootstrap.php
require 'init.php';
```

## Router

```php
$router = new Router();
$router->add('/', 'GET', HomeHandler::class);
$router->add('/users/:id', 'GET', UserByIdHandler::class, ['id' => '\d+']);
$router->add('/posts', 'GET', PostsHandler::class);
$router->add('/posts', 'POST', CreatePostHandler::class);

$router->dispatch($_SERVER['REQUEST_URI'] ?? '/', $_SERVER['REQUEST_METHOD'] ?? 'GET');
```

## Controller

```php
class UsersController extends Controller
{
    public function show()
    {
        $user = User::find(1);
        $this->setVar('user', $user);
        $this->render('show');
    }

    public function destroy()
    {
        $this->redirect('/users');
    }
}
```

## Database

```php
$pdo = Database::connection('default');
$stmt = $pdo->query('SELECT * FROM users')->fetchAll();

// Bulk operations
$queries = Database::bulkInsert('users', ['name', 'email'], [
    ['John', 'john@example.com'],
    ['Jane', 'jane@example.com'],
], false, 1000);
```

## Config

```php
$config = Config::get('main', ROOT . '/config/app.ini');
$debug = $config->get('debug');
$all = $config->toArray();
```

## Glue (JS/CSS Bundling)

```php
$css = new GlueCSS();
$css->addFile('css/base.css')
    ->addFile('css/layout.css')
    ->tag(); // returns <style>...</style>

$js = new GlueJS();
$js->addFile('js/base.js')
   ->addFile('js/app.js')
   ->tag(); // returns <script>...</script>
```

## Curl

```php
// Single request
$result = Curl::request('https://api.example.com/user', ['id' => 1]);

// Parallel requests
$results = Curl::parallel([
    ['url' => 'https://api.example.com/users'],
    ['url' => 'https://api.example.com/posts'],
]);
```

## Memcached

```php
$cache = new Memcached(['host' => '127.0.0.1', 'port' => 11211]);
$cache->set('users', $data, 3600);
$value = $cache->get('users');
```
