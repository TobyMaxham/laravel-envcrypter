<?php

namespace TobyMaxham\LaravelEnvCrypter;

class EnvCrypter
{
    private string $secretKey;

    private string $skip = 'SKIPPEDLINE';

    private string $algo;

    public function __construct(string $secretKey, string $algo = 'aes-256-cbc')
    {
        $this->secretKey = $secretKey;
        $this->algo = $algo;
    }

    public function encryptFile(string $filename)
    {
        return $this->cryptFile($filename, 'encrypt');
    }

    public function decryptFile(string $filename)
    {
        return $this->cryptFile($filename, 'decrypt');
    }

    private function cryptFile(string $filename, string $method)
    {
        $lines = array_filter(
            array_map($this->mapValues(), $this->loadLines($filename)),
            fn ($line) => $this->skip != $line
        );

        return implode("\n",
            array_map($this->{$method.'Line'}(), $lines)
        );
    }

    private function mapValues()
    {
        return function ($line) {
            if (empty($line) || 0 === strpos($line, '#')) {
                return $line; // skip comments
            }

            $result = \Dotenv\Parser\EntryParser::parse($line);
            if ($result instanceof \GrahamCampbell\ResultType\Success) {
                return $result;
            }

            return $this->skip;
        };
    }

    private function loadLines(string $filename): array
    {
        $content = file_get_contents($filename);

        /* @see \Dotenv\Parser\Parser::parse() */
        return preg_split("/(\r\n|\n|\r)/", $content);
    }

    public function decryptValue($value)
    {
        $parts = explode(':', base64_decode($value));

        return $this->callSslCrypt('decrypt', $parts[2], $parts[0], $parts[1]);
    }

    public function encryptValue($value)
    {
        // Salt to add entropy
        $salt = sha1(mt_rand());

        // Initialization Vector, randomly generated and saved each time
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->algo));
        $encrypted = $this->callSslCrypt('encrypt', $value, $salt, $iv);

        return base64_encode("$salt:$iv:$encrypted");
    }

    private function callSslCrypt(string $method, $value, $salt, $iv)
    {
        return call_user_func_array(
            'openssl_'.$method,
            [
                $value,
                $this->algo,
                sha1("{$salt}:{$this->secretKey}"),
                0,
                $iv,
            ]
        );
    }

    private function decryptLine()
    {
        return function ($line) {
            if (! $line instanceof \GrahamCampbell\ResultType\Success) {
                return $line;
            }

            /** @var \Dotenv\Parser\Entry $entry */
            $entry = $line->success()->get();
            $name = $entry->getName();

            /** @var \Dotenv\Parser\Value $value */
            $value = $entry->getValue()->get()->getChars();

            $decryptValue = $this->decryptValue($value);
            if (! is_string($decryptValue) && false == $decryptValue) {
                throw new \Exception('Encryption failed');
            }

            return $name.'='.$decryptValue;
        };
    }

    private function encryptLine()
    {
        return function ($line) {
            if (! $line instanceof \GrahamCampbell\ResultType\Success) {
                return $line;
            }

            /** @var \Dotenv\Parser\Entry $entry */
            $entry = $line->success()->get();
            $name = $entry->getName();

            /** @var \Dotenv\Parser\Value $value */
            $value = $entry->getValue()->get()->getChars();

            return $name.'='.$this->encryptValue($value);
        };
    }
}
