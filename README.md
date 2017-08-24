Laravel Resource Generator (Laravel5.4)
=======================

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/d70b34f6a08144f18d8dedd6da92f1db)](https://www.codacy.com/app/matmaxanh/laravel-generator?utm_source=github.com&utm_medium=referral&utm_content=matmaxanh/laravel-generator&utm_campaign=badger)

This package extend make laravel command to use custom template.
Further more, add some new command as generate mvc, create new package.

# Documentation is in process...

Documentation
--------------

1. [Installation](#installation)
2. [Publish](#publish)
3. [Generator Command](#generator--command)
4. [Option](#option)

## Installation

1. Add this package to your composer.json:

  ```bash
  composer require bluecode/laravel-generator
  ```

2. Run composer update

  ```bash
  composer update
  ```

3. Add the ServiceProviders to the providers array in ```config/app.php```<br>

  ```php
  Bluecode\Generator\GeneratorServiceProvider::class,
  ```

  As we are using [laravelcollective/html](https://github.com/LaravelCollective/html)as a dependency<br>
  so we need to add those ServiceProviders as well.

  ```php
  Collective\Html\HtmlServiceProvider::class,
  ```

  Also for convenience, add these facades in alias array in ```config/app.php```

  ```php
  'Form'      => Collective\Html\FormFacade::class,
  'Html'      => Collective\Html\HtmlFacade::class,
  ```

## Publish

1. Publish configuration file ```generator.php```

  ```bash
  php artisan vendor:publish --tag=laravel-generator.config
  ```

2. Publish template folder into ```resources/vendor/laravel-generator/templates```

  ```bash
  php artisan vendor:publish --tag=laravel-generator.template
  ```

## Generator Command

1. Generate Migration:

  ```bash
  php artisan gen:migrate MigrationName
  ```

e.g.

  ```bash
  php artisan gen:migrate create_posts_table
  ```

2. Generate Model:

  php artisan gen:model ModelName

e.g.

  ```bash
  php artisan gen:model Post
  ```

3. Generate Controller

  ```bash
  php artisan gen:model ModelName
  ```

e.g.

  ```bash
  php artisan gen:model Post
  ```

4. Generate View

  ```bash
  php artisan gen:view ViewName
  ```

e.g.

  ```bash
  php artisan gen:view index
  php artisan gen:view create
  ```

5. Generate MVC:

  ```bash
  php artisan gen:mvc ModelName
  ```

e.g.
  ```bash
  php artisan gen:mvc Post
  php artisan gen:mvc Post --actions=index,create,edit
  ```

6. Generate Package

```bash
  php artisan gen:package VendorName PackageName
  ```

e.g.
  ```bash
  php artisan gen:package Module Post
  php artisan gen:package Module Post --path=packages/post
  php artisan gen:package Module Post --actions=index,create,edit
  ```

## Option

Use --force or -f to overwrite exist file on all command.

Credits
--------

This Laravel Generator is created by [Bluecode](https://github.com/matmaxanh).

**Bugs & Forks are welcomed :)**
