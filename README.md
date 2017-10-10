# laravel-auth-client
![](https://habrastorage.org/webt/59/d5/42/59d542206afbe280817420.png)

This package brings a set of artisan commands that allows you work with Jincor Auth Service.

Installation
------------
You can install the package via composer:

`composer require jincor/laravel-auth-client`

Register the service provider:

```PHP
// config/app.php
 'providers' => [
     ...
     JincorTech\AuthClient\AuthClientServiceProvider::class,
 ],
 ```
 
 Usage
 -----
 Run `php artisan` and you'll see new commands under the `auth` namespace.
 
 ```$bash
 php artisan
 auth
  ...
  auth:login:tenant     Tenant login
  auth:register:tenant  Tenant registration
```

Credits
-------
* [Aleserche](https://github.com/Aleserche)
* [Jincor Team](https://jincor.com)

License
-------
The MIT License (MIT). Please see [License File](LICENSE) for more information.