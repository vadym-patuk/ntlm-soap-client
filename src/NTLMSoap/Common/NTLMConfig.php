<?php

namespace matejsvajger\NTLMSoap\Common;

use matejsvajger\NTLMSoap\Exception\RequiredConfigMissingException;

class NTLMConfig implements \Serializable, \Iterator
{
    private $parameters = [];

    protected $requiredParameters = [
        'domain', 'username', 'password'
    ];

    public function __construct(array $config)
    {
        if (null !== $config && is_array($config)) {
            $this->assertParametersAreValid($config);
            foreach ($config as $key => $value) {
                $this->{$key} = $value;
                $GLOBALS['NTLMClient' . ucfirst($key)] = $value;
            }
        }
    }

    /**
     * Returns a "<domain>/<username>:<password>" formated
     * string, required for NTLM Authentication headers.
     *
     * @author Matej Svajger <matej.svajger@koerbler.com>
     * @version 1.0
     * @date    2016-11-16
     *
     * @return  string     NTLM Auth String
     */
    public static function getAuthString()
    {
        $domain   = $GLOBALS['NTLMClientDomain'];
        $username = $GLOBALS['NTLMClientUsername'];
        $password = $GLOBALS['NTLMClientPassword'];

        return "{$domain}/{$username}:{$password}";
    }

    /**
     * Validates that all required parameters are set, otherwise throws an exception
     *
     * @author Matej Svajger <hello@matejsvajger.com>
     * @version 1.0
     * @date    2016-11-16
     *
     * @param   array      $parameters
     * @throws RequiredConfigMissingException
     */
    protected function assertParametersAreValid(array $parameters)
    {
        foreach ($this->requiredParameters as $parameter) {
            if (empty($parameters[$parameter])) {
                throw RequiredConfigMissingException::withItem($parameter);
            }
        }
    }

    public function __set(string $param, mixed $value): void
    {
        $this->parameters[$param] = $value;
    }

    public function __get(string $param): mixed
    {
        return $this->parameters[$param] ?? null;
    }

    public function serialize(): array
    {
        return serialize([
            'parameters' => $this->parameters
        ]);
    }

    public function unserialize($data): void
    {
        $data = unserialize($data);
        $this->parameters = $data['parameters'];
    }

    public function __serialize(): array
    {
        return [
            'parameters' => $this->parameters,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->parameters = $data['parameters'] ?? [];
    }

    public function rewind(): void
    {
        reset($this->parameters);
    }

    public function current(): mixed
    {
        return current($this->parameters);
    }

    public function key(): mixed
    {
        return key($this->parameters);
    }

    public function next(): void
    {
        next($this->parameters);
    }

    public function valid(): bool
    {
        return key($this->parameters) !== null;
    }
}
