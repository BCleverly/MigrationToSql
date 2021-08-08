<?php

namespace BCleverly\MigrationToSql;

use Spatie\LaravelPackageTools\{Package, PackageServiceProvider};
use BCleverly\MigrationToSql\Commands\MigrationToSqlCommand;

class MigrationToSqlServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('migrationtosql')
            ->hasConfigFile()
            ->hasCommand(MigrationToSqlCommand::class);
    }
}
