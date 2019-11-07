<?php

namespace MiladRahimi\Jwt\Cryptography\Algorithms\Hmac;

use MiladRahimi\Jwt\Cryptography\Signer;
use MiladRahimi\Jwt\Cryptography\Verifier;
use MiladRahimi\Jwt\Exceptions\InvalidKeyException;
use MiladRahimi\Jwt\Exceptions\InvalidSignatureException;
use MiladRahimi\Jwt\Exceptions\SigningException;

/**
 * Class AbstractHmac
 *
 * @package MiladRahimi\Jwt\Cryptography\Algorithms\Hmac
 */
abstract class AbstractHmac implements Signer, Verifier
{
    /**
     * @var string  Algorithm name
     */
    protected static $name;

    /**
     * @var string  Encryption key
     */
    protected $key;

    /**
     * AbstractHmac constructor.
     *
     * @param string $key
     * @throws InvalidKeyException
     */
    public function __construct(string $key)
    {
        $this->setKey($key);
    }

    /**
     * @inheritdoc
     */
    public function sign(string $message): string
    {
        $signature = hash_hmac($this->algorithmName(), $message, $this->key, true);

        if ($signature === false) {
            throw new SigningException();
        }

        return $signature;
    }

    /**
     * @inheritdoc
     */
    public function verify(string $plain, string $signature)
    {
        if ($signature != $this->sign($plain)) {
            throw new InvalidSignatureException();
        }
    }

    /**
     * Convert JWT algorithm name to hash function name
     *
     * @return string
     */
    protected function algorithmName(): string
    {
        return 'sha' . substr($this->getName(), 2);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return static::$name;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @throws InvalidKeyException
     */
    public function setKey(string $key)
    {
        if (strlen($key) < 32 || strlen($key) > 6144) {
            throw new InvalidKeyException();
        }

        $this->key = $key;
    }
}
