# This is my package MigrationToSql

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bcleverly/migrationtosql.svg?style=flat-square)](https://packagist.org/packages/bcleverly/migrationtosql)
[![Total Downloads](https://img.shields.io/packagist/dt/bcleverly/migrationtosql.svg?style=flat-square)](https://packagist.org/packages/cleverly/migrationtosql)

---
This repo is here to help you extract the SQL queries from your registered migration files. Running the below command will output each migration file in the order they're registered.
```bash
php artisan migrate:to-sql
```
Below is an example of the output of the CreateUsersTable
```sql
-- CreateUsersTable
-- \laravelapp\database\migrations/2014_10_12_000000_create_users_table.php
create table `users` (
  `id` bigint unsigned not null auto_increment primary key,
  `name` varchar(255) not null,
  `email` varchar(255) not null,
  `email_verified_at` timestamp null,
  `password` varchar(255) not null,
  `remember_token` varchar(100) null,
  `created_at` timestamp null,
  `updated_at` timestamp null
) default character set utf8mb4 collate 'utf8mb4_unicode_ci'
```

## Testing
TODO
```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
