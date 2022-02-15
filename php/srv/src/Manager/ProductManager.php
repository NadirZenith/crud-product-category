<?php

namespace App\Manager;

use App\Service\ExchangeService;

class ProductManager
{

    protected ExchangeService $exchangeService;

    public function __construct(ExchangeService $exchangeService)
    {
        $this->exchangeService = $exchangeService;
    }

    public function toCurrency($currency, array $products): array
    {

        foreach ($products as $product) {
            $price = $product->getPrice();
            $productCurrency = $product->getCurrency();

            if ('EUR' === $productCurrency && 'USD' === $currency) {
                $price = $this->exchangeService->getPriceUSD($price);
            } elseif ('USD' === $productCurrency && 'EUR' === $currency) {
                $price = $this->exchangeService->getPriceEUR($price);
            }

            $product->setPrice($price);
            $product->setCurrency($currency);
        }

        return $products;
    }
}
