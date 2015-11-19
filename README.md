Laravel Scaffold/CRUD Generator (Laravel5.1)
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

And your simple CRUD and APIs are ready in mere seconds.

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
5. [Customization](#customization)
	1. [Base Controller](#base-controller)
	2. [Customize Templates](#customize-templates)
6. [Options](#options)
	1. [Paginate Records](#paginate-records)
	2. [Model Soft Deletes](#model-soft-deletes)
	3. [Fields From File](#fields-from-file)
	4. [Custom Table Name](#custom-table-name)
	5. [Skip Migration](#skip-migration)
	6. [Remember Token](#remember-token)
7. [Generator from existing tables](#generator-from-existing-tables)

## Installation

1. Add this package to your composer.json:
  
        "require": {
            "laracasts/flash": "dev-master",
            "laravelcollective/html": "5.1.*@dev",
            "matmaxanh/laravel-generator": "dev-master"
        }
  
2. Run composer update

        composer update
    
3. Add the ServiceProviders to the providers array in ```config/app.php```.<br>
   As we are using these two packages [laravelcollective/html](https://github.com/LaravelCollective/html) & [laracasts/flash](https://github.com/laracasts/flash) as a dependency.<br>
   so we need to add those ServiceProviders as well.

		Collective\Html\HtmlServiceProvider::class,
		Laracasts\Flash\FlashServiceProvider::class,
		matmaxanh\Generator\GeneratorServiceProvider::class,
        
   Also for convenience, add these facades in alias array in ```config/app.php```.

		'Form'      => Collective\Html\FormFacade::class,
		'Html'      => Collective\Html\HtmlFacade::class,
		'Flash'     => Laracasts\Flash\Flash::class

## Configuration

Publish Configuration file ```generator.php```.

        php artisan vendor:publish --provider="Bluecode\Generator\GeneratorServiceProvider"
        
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

## Publish & Initialization

Mainly, we need to do three basic things to get started.
1. Publish some common views like ```paginate.blade.php```.
        php artisan generate:publish

## Generator

Fire artisan command to generate API, Scaffold with CRUD views or both API as well as CRUD views.

Generate CRUD Scaffold:
 
        php artisan generate:scaffold ModelName

Generate Model With Validation And Relationships:

        php artisan generate:model ModelName
        
Generate Migrate From Exist Tables:
        
        php artisan generate:migrate
        
e.g.
    
    php artisan Bluecode.generator:scaffold Project
    php artisan Bluecode.generator:scaffold Post

    php artisan Bluecode.generator:model Project
    php artisan Bluecode.generator:model Post

    php artisan Bluecode.generator:migrate
    php artisan Bluecode.generator:migrate projects

Here is the sample [fields input json](https://github.com/matmaxanh/laravel-generator/blob/master/samples/fields.json)

## Supported HTML Field Types

Here is the list of supported field types with options:
  * text
  * textarea
  * password
  * email
  * file
  * checkbox
  * radio:male,female,option3,option4
  * number
  * date
  * select:India,USA

## Customization

### Customize Templates

To use your own custom templates,

1. Publish templates to  ```/resources/generator-templates```

        php artisan generate:publish --templates

2. Leave only those templates that you want to change. Remove the templates that do not plan to change.

## Options

### Paginate Records

To paginate records, you can specify paginate option,
e.g.

        php artisan generate:scaffold Post --paginate=10

### Model Soft Deletes

To use SoftDelete, use softDelete option,

        php artisan generate:scaffold Post --softDelete

### Fields From File

If you want to pass fields from file then you can create fields json file and pass it via command line. Here is the sample [fields.json](https://github.com/matmaxanh/laravel-generator/blob/master/samples/fields.json)

You have to pass option ```--fieldsFile=absolute_file_path_or_path_from_base_directory``` with command. e.g.

         php artisan generate:scaffold Post --fieldsFile="/Users/Local/laravel-generator/fields.json"
         php artisan generate:scaffold Post --fieldsFile="fields.json"

### Custom Table Name

You can also specify your own custom table name by,

        php artisan generate:scaffold Post --tableName=custom_table_name

### Skip Migration

You can also skip migration generation,

        php artisan generate:scaffold Post --skipMigration

### Remember Token

To generate rememberToken field in migration file,

        php artisan generate:scaffold Post --rememberToken

## Generator from existing tables

To use generator with existing table, you can specify ```--fromTable``` option. ```--tableName``` option is required and you need to specify table name.

Just make sure, you have installed ```doctrine/dbal``` package.

**Limitation:** As of now it is not fully working (work is in progress). It will not create migration file. You need to tweak some of the things in your generated files like timestamps, primary key etc. 

        php artisan generate:scaffold Post --fromTable --tableName=posts

Credits
--------

This API Generator is created by [matmaxanh](https://github.com/matmaxanh).

**Bugs & Forks are welcomed :)**
