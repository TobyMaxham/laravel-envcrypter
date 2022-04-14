<?php

require_once 'vendor/autoload.php';

use TobyMaxham\LaravelEnvCrypter\EnvCrypter;

$space = '   ';
$yellow = "\e[33m";
$green = "\e[32m";
$reset = "\e[0m";
$n = "\n";

if (! isset($argv) || 3 != count($argv) || '' == $argv[2]
    || '-h' == $argv[1] || '--help' == $argv[1]
    || ! in_array($argv[1], ['load', 'store'])) {
    echo "{$n}";
    echo "{$yellow}Description:{$reset}{$n}{$space}This Command will decrypt/encrypt your environment variables.{$n}{$n}";
    echo "{$yellow}Usage:{$reset}{$n}{$space}php console.php <action> <crypkey>{$n}{$n}";
    echo "{$yellow}Arguments:{$reset}{$n}{$space}{$green}action{$reset}\t The action can be `load` or `store`.{$n}";
    echo "{$space}{$green}crypkey{$reset}\t The key to decrypt/encrypt the environment file.{$n}";
    exit(1);
}

$crypter = new EnvCrypter($argv[2]);

if ('load' == $argv[1]) {
    $content = $crypter->decryptFile('.env.repository');
    file_put_contents('.env', $content);

    echo "{$yellow}Done!{$reset}{$n}";
    echo "{$yellow}Please commit the new file!{$reset}{$n}";
} elseif ('store' == $argv[1]) {
    $content = $crypter->encryptFile('.env');
    file_put_contents('.env.repository', $content);

    echo "{$yellow}Done!{$reset}{$n}";
    echo "{$yellow}Please set development variables and run `php artisan key:generate`!{$reset}{$n}";
}

exit(0);
