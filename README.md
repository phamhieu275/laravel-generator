Laravel Resource Generator (Laravel5.1)
======================= 

The artisan command can generate the following items:
  * Migration File
  * Model
  * Repository
  * Service
  * Controller
  * View
    * index.blade.php
    * show.blade.php
    * create.blade.php
    * edit.blade.php
    * form.blade.php
  * adjusts routes.php
  * adjusts ModelFactory.php

Here is the full documentation.

[Upgrade Guide](https://github.com/matmaxanh/laravel-generator/blob/master/Upgrade_Guide.md).

# Documentation is in process...

Documentation
--------------

1. [Installation](#installation)
2. [Configuration](#configuration)
3. [Publish & Initialization](#publish--initialization)
4. [Generator](#generator)
5. [Supported Field Types](#supported-field-types)
5. [Customize Templates](#customize-templates)
6. [Options](#options)
	1. [Paginate Records](#paginate-records)
	2. [Auth triat](#model-auth)

## Installation

1. Add this package to your composer.json:
  
        "require": {
            "doctrine/dbal": "^2.5",
            "laracasts/flash": "dev-master",
            "laravelcollective/html": "5.1.*@dev",
            "bluecode/laravel-generator": "dev-master"
        }
  
2. Run composer update

        composer update
    
3. Add the ServiceProviders to the providers array in ```config/app.php```.<br>
   As we are using these two packages [laravelcollective/html](https://github.com/LaravelCollective/html) & [laracasts/flash](https://github.com/laracasts/flash) as a dependency.<br>
   so we need to add those ServiceProviders as well.

		Collective\Html\HtmlServiceProvider::class,
		Laracasts\Flash\FlashServiceProvider::class,
		Bluecode\Generator\GeneratorServiceProvider::class,
        
   Also for convenience, add these facades in alias array in ```config/app.php```.

		'Form'      => Collective\Html\FormFacade::class,
		'Html'      => Collective\Html\HtmlFacade::class,
		'Flash'     => Laracasts\Flash\Flash::class

## Configuration

Publish Configuration file ```generator.php```.

        php artisan vendor:publish
        
Config file (```config/generator.php```) contains path for all generated files

```base_controller``` - Base Controller for all Controllers<br>

```path_migration``` - Path where Migration file to be generated<br>
```path_model``` - Path where Model file to be generated<br>
```path_repository``` - Path where Repository file to be generated<br>
```path_service``` - Path where Service file to be generated<br>
```path_controller``` - Path where Controller file to be generated<br>
```path_views``` - Path where views will be created<br>
```path_request``` -  Path where request file will be created<br>
```path_routes``` - Path of routes.php (if you are using any custom routes file)<br>

```namespace_model``` - Namespace of Model<br>
```namespace_repository``` - Namespace of Repository<br>
```namespace_service``` - Namespace of Service<br>
```namespace_controller``` - Namespace of Controller<br>
```namespace_request``` - Namespace for Request<br>

```model_extend_class``` - Extend class of Models<br>

```main_layout``` - Extend master layout<br>

```route_prefix``` - Prefix of scaffold route<br>

```use_repository_layer``` - Using repository layer<br>

```use_service_layer``` - Using service layer<br>

## Publish & Initialization

1. Publish some common views like ```paginate.blade.php```.
        php artisan generator:publish

2. Publish template.
        php artisan generator:publish --templates

3. Publish a base repository file
        php artisan generator:publish --baseRepository

## Generator

Fire artisan command to generate Migration, Model, Scaffold with CRUD views from exist tables.
This package can generate files from a specify table or from all tables in database.

This package require you to pass at least one argument for table name.
If you want to pass many table name, a list table name will separate by comma.

Generate Migration From Exist Tables:
  
        php artisan generator:make:migrate TableName

Generate CRUD Scaffold:
 
        php artisan generator:make:scaffold TableName

Generate Model With Validation And Relationships:

        php artisan generator:make:model TableName

Generate Factory From Exist Tables:

        php artisan generator:make:factory TableName

Generate All Resource File:

        php artisan generator:make:resource TableName
        
e.g.
    php artisan generator:migrate
    php artisan generator:migrate posts,comments

    php artisan generator:make:model 
    php artisan generator:make:model posts,comments
    php artisan generator:make:model --tables=posts,comments
    php artisan generator:make:model --ignore=posts,comments
    php artisan generator:make:model posts,comments --models=Post,Comment

    php artisan generator:make:scaffold
    php artisan generator:make:scaffold posts,comments
    php artisan generator:make:scaffold --tables=posts,comments
    php artisan generator:make:scaffold --ignore=posts,comments

    php artisan generator:make:factory posts,comments

    php artisan generator:make:resource posts,comments

## Supported HTML Field Types

Here is the list of supported field types with options:
  * text
  * textarea
  * password
  * email
  * checkbox
  * number
  * date

## Customize Templates

To use your own custom templates,

1. Publish templates to  ```/resources/generator-templates```

        php artisan generator:publish --templates

2. Leave only those templates that you want to change. Remove the templates that do not plan to change.

## Options

### Paginate Records

To paginate records, you can specify paginate option,
e.g.

        php artisan generator:make:scaffold posts --paginate=10

### Model use Auth

To use Auth trait, use auth option,

        php artisan generator:make:model users --auth

Credits
--------

This Laravel Generator is created by [Bluecode](https://github.com/matmaxanh).

**Bugs & Forks are welcomed :)**
