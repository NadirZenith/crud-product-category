<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductTest extends WebTestCase
{
    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass(): void
    {
        $client = static::createClient();

        $connection = $client->getContainer()->get('doctrine')->getConnection();

        $connection->query('SET FOREIGN_KEY_CHECKS=0;');
        $connection->query('TRUNCATE TABLE product');
        $connection->query('TRUNCATE TABLE category');
        $connection->query('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function testCreateProduct(): void
    {

        $client = static::createClient();

        // demos cats
        $crawler = $client->request('POST', '/api/category', [], [], [], json_encode([
            'name' => 'firstcat'
        ]));
        $crawler = $client->request('POST', '/api/category', [], [], [], json_encode([
            'name' => 'secondcat'
        ]));

        $crawler = $client->request('POST', '/api/product', [], [], [], json_encode([
            'name' => 'first product',
            'price' => 1.25,
            'currency' => 'USD',
            'featured' => false,
            'category' => 2,
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $content = $client->getResponse()->getContent();

        $this->assertStringContainsString('first product', $content);
        $this->assertStringContainsString('secondcat', $content);
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


    public function testFeaturedProducts(): void
    {
        // p1
        $client = static::createClient();
        $crawler = $client->request('POST', '/api/product', [], [], [], json_encode([
            'name' => 'third product',
            'price' => 1.35,
            'currency' => 'USD',
            'featured' => true,
        ]));

        // p2
        $client = static::createClient();
        $crawler = $client->request('POST', '/api/product', [], [], [], json_encode([
            'name' => 'fourth product',
            'price' => 1.45,
            'currency' => 'EUR',
            'featured' => true,
            'category' => 2,
        ]));


        $client = static::createClient();
        $crawler = $client->request('GET', '/api/product/featured?currency=USD');

        $this->assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent();

        $this->assertStringContainsString('third product', $content);
        $this->assertStringContainsString('fourth product', $content);
        $this->assertStringNotContainsString('first product', $content);
        $this->assertStringNotContainsString('EUR', $content);
    }
}
