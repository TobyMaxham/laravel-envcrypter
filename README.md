# TobyMaxham Laravel EnvCrypter

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tobymaxham/laravel-envcrypter.svg?style=flat-square)](https://packagist.org/packages/tobymaxham/laravel-envcrypter)
[![Total Downloads](https://img.shields.io/packagist/dt/tobymaxham/laravel-envcrypter.svg?style=flat-square)](https://packagist.org/packages/tobymaxham/laravel-envcrypter)


This package can be used to encrypt and decrypt your `.env` file in any **Laravel** Application.

## Installation

You can install the package via composer:

```bash
composer require tobymaxham/laravel-envcrypter
```

## Intro

In modern application we use the `.env` file to store sensitive data like Database-Credentials or API-Tokens in it.
The benefit of that is, that none of them exists in your application source code and not in version-control.

But when it comes to share these credentials it's quite hard to share it in a secure way.
So when you add some new environment variables you have to share them with your team-mates or need them at least to update it on your production server.

**That's where this package can help you.**

With this package you can store your `.env` file encrypted in your repository and add any information you made in a secure way and share them with your team-mates.
When your local `.env` file needs to be updated, you can decrypt the file. Only the "Secret Token" will be needed to decrypt the file.


## Usage

Lets assume you have a file `.env.repository` where you want to store all the variables of your
local `.env` file. You can use the following code to encrypt all the variables and store it in
the file you want to commit in your applications repository:

```php
$crypter = new \TobyMaxham\LaravelEnvCrypter\EnvCrypter('Your-Secret-Token');

$content = $crypter->encryptFile('.env'); // encrypt the .env-File
file_put_contents('.env.repository', $content);
```

No you can commit your file and share it to your team-mates or upload it to your server.

When someone need to update the local `.env` file, just use the following code to decrypt the variables:

```php
$crypter = new \TobyMaxham\LaravelEnvCrypter\EnvCrypter('Your-Secret-Token');

$content = $crypter->decryptFile('.env.repository'); // decrypt the file
file_put_contents('.env', $content); // update your local file
```

Also see the example file to use this package as very simple command-line tool for storing and loading your environment files.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

If you've found a bug regarding security please mail git@maxham.de instead of using the issue tracker.

## Support me

[![ko-fi](https://ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/Z8Z4NZKU)

## Credits

- [TobyMaxham](https://github.com/TobyMaxham)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
