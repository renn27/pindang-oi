<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $connection = \Illuminate\Support\Facades\DB::connection();
        if ($connection instanceof \Illuminate\Database\SQLiteConnection) {
            $pdo = $connection->getPdo();
            
            $pdo->sqliteCreateFunction('DATEDIFF', function ($date1, $date2) {
                if (!$date1 || !$date2) return 0;
                try {
                    $d1 = new \DateTime($date1);
                    $d2 = new \DateTime($date2);
                    return (int) $d2->diff($d1)->format('%r%a');
                } catch (\Exception $e) {
                    return 0;
                }
            });

            $pdo->sqliteCreateFunction('GREATEST', function (...$args) {
                $args = array_filter($args, fn($v) => !is_null($v) && $v !== '');
                if (empty($args)) return null;
                return max($args);
            });

            $pdo->sqliteCreateFunction('LEAST', function (...$args) {
                $args = array_filter($args, fn($v) => !is_null($v) && $v !== '');
                if (empty($args)) return null;
                return min($args);
            });
        }
    }
}
