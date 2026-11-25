<?php

namespace Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use LogicException;

abstract class TestCase extends BaseTestCase
{
    public function createApplication(): Application
    {
        $app = parent::createApplication();
        $connection = $app['db']->connection();

        if (! $app->environment('testing') || $connection->getDriverName() !== 'sqlite' || $connection->getDatabaseName() !== ':memory:') {
            throw new LogicException('A suíte exige APP_ENV=testing e SQLite em memória antes de executar as migrations.');
        }

        return $app;
    }
}
