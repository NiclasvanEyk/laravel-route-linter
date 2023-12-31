# Statically validate your route definitions

## DISCLAIMER

This is repository is very much work in progress. I have decided to not publish this as its own package, but rather prepare a PR to Larastan. This will happen when proper reporting of unknown routes and route parameters is implemented.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/niclasvaneyk/laravel-route-linter.svg?style=flat-square)](https://packagist.org/packages/niclasvaneyk/laravel-route-linter)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/niclasvaneyk/laravel-route-linter/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/niclasvaneyk/laravel-route-linter/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/niclasvaneyk/laravel-route-linter/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/niclasvaneyk/laravel-route-linter/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/niclasvaneyk/laravel-route-linter.svg?style=flat-square)](https://packagist.org/packages/niclasvaneyk/laravel-route-linter)

Finds errors or potentially misleading definitions when registering Laravel routes.
Given the following routes file:

```php
Route::get('/articles/{slug}', function (Article $article) {
    return view('articles.detail', ['article' => $article]);
});

Route::get('/articles/new', function () {
    return view('articles.new');
});

Route::post('/articles/{slug}/comments/{id}', function (string $id, string $slug) {
    // ...
});
```

you can run this command to find potential issues:

```
php artisan route:lint

   ERROR  Potential problems found:  

ConfusingImplicitPathParameterBindings
The controller function parameters of POST articles/{slug}/comments/{id} are misleading. Their order in the path is ["slug", "id"], but in the controller the order is ["id", "slug"]. See https://laravel.com/docs/routing#required-parameters for more information.

RouteShadowed
The route GET|HEAD articles/new clashes with the existing route GET|HEAD articles/{slug}. Register it earlier to prevent this issue.
```

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-route-linter.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-route-linter)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require niclasvaneyk/laravel-route-linter
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-route-linter-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-route-linter-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="laravel-route-linter-views"
```

## Usage

```php
$laravelRouteLinter = new NiclasVanEyk\LaravelRouteLinter();
echo $laravelRouteLinter->echoPhrase('Hello, NiclasVanEyk!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Niclas van Eyk](https://github.com/NiclasVanEyk)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
