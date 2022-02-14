<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductTest extends WebTestCase
{
    public function testCreateProduct(): void
    {

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/product', [], [], [], json_encode([
            'name' => 'first product',
            'price' => 1.25,
            'currency' => 'USD',
            'featured' => false,
            'category' => 2,
        ]));

        $this->assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent();

        $this->assertStringContainsString('first product', $content);
    }

    public function testAllProducts(): void
    {

        $client = static::createClient();
        $crawler = $client->request('GET', '/api/product');

        $this->assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent();

        $this->assertStringContainsString('first product', $content);
    }

    public function testCreateProductErrorCurrency(): void
    {

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/product', [], [], [], json_encode([
            'name' => 'second product error',
            'price' => 1.27,
            'currency' => 'CHF',
            'featured' => false,
            'category' => 2,
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $content = $client->getResponse()->getContent();

        $this->assertStringContainsString('errors', $content);
        $this->assertStringContainsString('currency', $content);
        $this->assertStringContainsString('The value you selected is not a valid choice', $content);
    }

    public function testCreateProductErrorPrice(): void
    {

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/product', [], [], [], json_encode([
            'name' => 'second product error',
            'currency' => 'EUR',
            'featured' => false,
            'category' => 2,
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $content = $client->getResponse()->getContent();

        $this->assertStringContainsString('errors', $content);
        $this->assertStringContainsString('price', $content);
        $this->assertStringContainsString('This value should not be blank', $content);
    }
}
