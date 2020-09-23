<?php

declare(strict_types=1);

namespace Cube43\Ebics;

use Exception;
use Symfony\Component\HttpClient\HttpClient as SymfonyClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function str_replace;
use function strpos;
use function strstr;

class EbicsServerCaller
{
    private HttpClientInterface $httpClient;

    public function __construct(?HttpClientInterface $httpClient = null)
    {
        $this->httpClient = $httpClient ?? SymfonyClient::create();
    }

    public function __invoke(string $request, Bank $bank): string
    {
        $result = $this->httpClient->request('POST', $bank->getUrl(), [
            'headers' => ['Content-Type' => 'text/xml; charset=ISO-8859-1'],
            'body' => $request,
            'verify_peer' => false,
            'verify_host' => false,
        ])->getContent();

        if (! strpos($result, '<ReturnCode>000000</ReturnCode>')) {
            throw new Exception('Error' . strstr(str_replace('<ReportText>', '', (strstr($result, '<ReportText>') ?? '')), '</ReportText>', true));
        }

        return $result;
    }
}
