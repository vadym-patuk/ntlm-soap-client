<?php

namespace matejsvajger\NTLMSoap\Model\Traits;

use matejsvajger\NTLMSoap\Common\NTLMConfig;

trait NTLMRequest
{
    /**
     * @var array|null
     */
    private ?array $__last_request_headers = null;

    /**
     * @var string|null
     */
    private ?string $__last_request = null;

    /**
     * Replaces native php \SoapClient function and sends
     * request with NTLM Authentication headers to NAT
     * server.
     *
     * @author Matej Svajger <hello@matejsvajger.com>
     * @version 1.0
     * @date    2016-11-15
     */    
    public function __doRequest(string $request, string $location, string $action, int $version, bool $oneWay = false): ?string
    {
        $auth = NTLMConfig::getAuthString();

        $headers = [
            'Method: POST',
            'Connection: Keep-Alive',
            'User-Agent: PHP-SOAP-CURL',
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: "'.$action.'"',
        ];

        $this->__last_request_headers = $headers;
        $this->__last_request = $request;

        $ch = curl_init($location);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
        curl_setopt($ch, CURLOPT_USERPWD, $auth);

        $response = curl_exec($ch);

        return $response;
    }

    public function getLastRequestHeaders(): ?array
    {
        return $this->__last_request_headers;
    }

    public function getLastRequest(): ?string
    {
        return $this->__last_request;
    }    
}
