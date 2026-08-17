<?php

class TestController
{
    public function assert(mixed $expected, mixed $actual, string $message = ''): void
    {
        if ($expected === $actual) {
            echo "✓ {$message}\n";
        } else {
            echo "✗ Expected: " . var_export($expected, true) . "\n";
            echo "  Actual: " . var_export($actual, true) . "\n";
            echo "  {$message}\n\n";
            throw new AssertionError($message);
        }
    }

    public function preVarDump(mixed ...$args): void
    {
        echo "<pre>";
        var_dump(...$args);
        echo "</pre>";
    }

    public function count(int $expected, array $actual, string $message = ''): void
    {
        $this->assert($expected, count($actual), $message ?: "Array count should be {$expected}");
    }
}

// Uso:
// $test = new TestController();
// $test->assert(2, 1 + 1, 'Math works');
// $test->count(3, [1, 2, 3], 'Array has 3 items');
// $test->preVarDump(['key' => 'value']);
