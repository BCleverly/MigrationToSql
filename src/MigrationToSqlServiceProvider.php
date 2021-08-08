<?php

namespace Cleverly\MigrationToSql;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Cleverly\MigrationToSql\Commands\MigrationToSqlCommand;

class MigrationToSqlServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('migrationtosql')
            ->hasConfigFile()
            ->hasCommand(MigrationToSqlCommand::class);
    }
}
