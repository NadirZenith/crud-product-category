<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExchangeService
{

    const URL = 'http://api.exchangeratesapi.io';

    protected $client;

    protected $caches = [];

    public function __construct(HttpClientInterface $client, string $exchangeApiToken)
    {
        $this->client = $client;
        $this->token = $exchangeApiToken;
    }

    public function getPriceUSD($eurPrice): float
    {
        $quote = $this->getQuote('EUR', 'USD', $eurPrice);

        return $eurPrice * $quote;
    }

    public function getPriceEUR($usdPrice): float
    {
        $quote = $this->getQuote('USD', 'EUR', $usdPrice);

        return $usdPrice * $quote;
    }

    private function getQuote($base, $symbol): float
    {

        if (isset($this->caches[$base . $symbol])) {
            return $this->caches[$base . $symbol];
        }

        $result = $this->callQuote('EUR', 'USD');

        $this->caches[$base . $symbol] = $result;
        $this->caches[$symbol . $base] = 1 / $result;

        return $this->caches[$base . $symbol];
    }

    private function callQuote($base, $symbol)
    {
        $query = http_build_query([
            'access_key' => $this->token,
            'base' => $base,
            'symbols' => $symbol,

        ]);
        $response = $this->client->request('GET', self::URL . '/latest?' . $query);

        $result = json_decode($response->getContent(), true);

        return $result['rates'][$symbol];
    }
}
