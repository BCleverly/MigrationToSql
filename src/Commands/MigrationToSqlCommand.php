<?php

namespace Cleverly\MigrationToSql\Commands;

use Doctrine\SqlFormatter\NullHighlighter;
use Doctrine\SqlFormatter\SqlFormatter;
use Illuminate\Console\Command;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use ReflectionClass;

class MigrationToSqlCommand extends Command
{
    public $signature = 'migrate:to-sql';

    public $description = 'Gets all the sql from your registered migration files';

    private array $migrationQueries = [];

    private Filesystem $files;

    private Migrator $migrator;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
        $this->migrator = app('migrator');
    }

    public function handle(): int
    {
        $this->info('Loading migrations...');

        $migrations = $this->migrator->getMigrationFiles(array_merge($this->migrator->paths(), [
            $this->laravel->databasePath() . DIRECTORY_SEPARATOR . 'migrations'
        ]));

        $this->info('Loading extracting queries...');

        foreach($migrations as $file){
            $migration = $this->resolvePath($file);
            foreach ($this->getQueries($migration, 'up') as $query) {
                $name = get_class($migration);

                $reflectionClass = new ReflectionClass($migration);

                if ($reflectionClass->isAnonymous()) {
                    $name = $this->getMigrationName($reflectionClass->getFileName());
                }

                $this->migrationQueries[] = [
                    'name' => $name,
                    'file' => $file,
                    'query' => $query['query']
                ];
            }
        }



        $migrationQueriesOutput = '';
        foreach($this->migrationQueries as $query) {
            $formattedQuery = (new SqlFormatter(new NullHighlighter))->format($query['query']);
            $migrationQueriesOutput .= "-- {$query['name']}
-- {$query['file']}
{$formattedQuery}
\n\n";
        }

        $sqlDumpPath = database_path('sql');
        $sqlDumpFile = $sqlDumpPath . DIRECTORY_SEPARATOR . 'migration-to-sql.sql';
        $this->info('Create sql file: ' . $sqlDumpFile);

        if ($this->files->exists($sqlDumpPath) === false) {
            $this->files->makeDirectory($sqlDumpPath, 0755, true);
        }

        $this->files->put($sqlDumpFile, $migrationQueriesOutput);
        $this->info('All done');
        return 0;
    }

    protected function resolvePath(string $path)
    {
        $class = $this->getMigrationClass($this->getMigrationName($path));

        if (class_exists($class) && realpath($path) == (new ReflectionClass($class))->getFileName()) {
            return new $class;
        }

        $migration = $this->files->getRequire($path);

        return is_object($migration) ? $migration : new $class;
    }

    public function getMigrationName($path)
    {
        return str_replace('.php', '', basename($path));
    }

    protected function getMigrationClass(string $migrationName): string
    {
        return Str::studly(implode('_', array_slice(explode('_', $migrationName), 4)));
    }

    protected function getQueries($migration, $method)
    {
        $db = $this->migrator->resolveConnection(
            $migration->getConnection()
        );

        return $db->pretend(function () use ($migration, $method) {
            if (method_exists($migration, $method)) {
                $migration->{$method}();
            }
        });
    }
}
