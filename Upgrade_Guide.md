Laravel Scaffold/CRUD Generator Upgrade Guide (Laravel5)
=======================

Upgrade Guide from 1.0 to 1.x 
-------------------------------------

1. Take a backup of your ```config/generator.php```

2. Delete your ```config/generator.php```

3. Change version in composer.json

        "require": {
            "bluecode/laravel-generator": "1.0"
        }

4. Run composer update.

5. Run publish command again.

        php artisan vendor:publish --provider="Bluecode\Generator\GeneratorServiceProvider"

6. Replace your custom paths again in ```config/generator.php```.

7. Enjoy Upgrade :)
