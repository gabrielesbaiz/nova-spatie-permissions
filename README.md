# NovaSpatiePermissions

[![Latest Version on Packagist](https://img.shields.io/packagist/v/gabrielesbaiz/nova-spatie-permissions.svg?style=flat-square)](https://packagist.org/packages/gabrielesbaiz/nova-spatie-permissions)
[![Total Downloads](https://img.shields.io/packagist/dt/gabrielesbaiz/nova-spatie-permissions.svg?style=flat-square)](https://packagist.org/packages/gabrielesbaiz/nova-spatie-permissions)

A Laravel Nova tool for the Spatie Permission package.

Original code from [moroneyio/novaspatiepermissions](https://github.com/moroneyio/novaspatiepermissions)

## Features

- ✅ Manage roles and permissions on the Nova dashboard
- ✅ Use permissions based authorization for Nova resources

## Installation

You can install the package via composer:

```bash
composer require gabrielesbaiz/nova-spatie-permissions
```

Next, if you do not have package discovery enabled, you need to register the provider in the `config/app.php` file.

```php
'providers' => [
    ...,
    Gabrielesbaiz\NovaSpatiePermissions\NovaSpatiePermissionsServiceProvider::class,
]
```

Next, you must register the tool with Nova. This is typically done in the `tools` method of the `NovaServiceProvider`.

```php
// in app/Providers/NovaServiceProvider.php
use Gabrielesbaiz\NovaSpatiePermissions\Novaspatiepermissions;

public function tools()
{
    return [
        // ...
        Novaspatiepermissions::make(),
    ];
}
```

Next, add `MorphToMany` fields to your `app/Nova/User` resource:

```php
use Laravel\Nova\Fields\MorphToMany;

public function fields(Request $request)
{
    return [
        // ...
        MorphToMany::make('Roles', 'roles', \Gabrielesbaiz\NovaSpatiePermissions\Role::class),
        MorphToMany::make('Permissions', 'permissions', \Gabrielesbaiz\NovaSpatiePermissions\Permission::class),
    ];
}
```

Finally, add the `ForgetCachedPermissions` class to your `config/nova.php` middleware like so:

```php
// in config/nova.php
'middleware' => [
	'web',
	Authenticate::class,
	DispatchServingNovaEvent::class,
	BootTools::class,
	Authorize::class,
	 \Gabrielesbaiz\NovaSpatiePermissions\ForgetCachedPermissions::class,
],
```

## Localization

You can use the artisan command line tool to publish localization files:

```php
php artisan vendor:publish --provider=" \Gabrielesbaiz\NovaSpatiePermissions\NovaPermissionServiceProvider"
```

## Usage

```php
$novaSpatiePermissions = new Gabrielesbaiz\NovaSpatiePermissions();
echo $novaSpatiePermissions->echoPhrase('Hello, Gabrielesbaiz!');
```

## Permissions Based Authorization for Nova Resources
By default, Laravel Nova uses Policy based authorization for Nova resources. If you are using the Spatie Permission library, it is very likely that you would want to swap this out to permission based authorization without the need to define Authorization policies.

To do so, you can use the `PermissionsBasedAuthTrait` and define a `permissionsForAbilities` static array property in your Nova resource class like so:

```php
// in app/Nova/YourNovaResource.php

class YourNovaResource extends Resource
{
    use \Gabrielesbaiz\NovaSpatiePermissions\PermissionsBasedAuthTrait;

    public static $permissionsForAbilities = [
      'all' => 'manage products',
    ];
}
```

The example above means that all actions on this resource can be performed by users who have the "manage products" permission. You can also define separate permissions for each action like so:

```php
    public static $permissionsForAbilities = [
      'viewAny' => 'view products',
      'view' => 'view products',
      'create' => 'create products',
      'update' => 'update products',
      'delete' => 'delete products',
      'restore' => 'restore products',
      'forceDelete' => 'forceDelete products',
      'addAttribute' => 'add product attributes',
      'attachAttribute' => 'attach product attributes',
      'detachAttribute' => 'detach product attributes',
    ];
```

### Relationships 
To allow your users to specify a relationship on your model, you will need to add another permission on the Model. 
For example, if your `Product` belongs to `User`, add the following permission on `app/Nova/User.php`. : 

```php
    public static $permissionsForAbilities = [
      'addProduct' => 'add user on products'
    ];
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Joshua](https://github.com/moroneyio)
- [Gabriele Sbaiz](https://github.com/gabrielesbaiz)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
