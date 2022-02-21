<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CategoryTest extends WebTestCase
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

    public function testCreateCategory(): void
    {

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/categories', [], [], [], json_encode([
            'name' => 'firstcat'
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $content = $client->getResponse()->getContent();

        $this->assertStringContainsString('firstcat', $content);

        $crawler = $client->request('POST', '/api/categories', [], [], [], json_encode([
            'name' => 'secondcat'
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $content = $client->getResponse()->getContent();

        $this->assertStringContainsString('secondcat', $content);
    }

    public function testCreateCategoryErrorMissName(): void
    {

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/categories', [], [], [], json_encode([
            'description' => 'miss name'
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $content = $client->getResponse()->getContent();

        $this->assertStringContainsString('errors', $content);
        $this->assertStringContainsString('name', $content);
        $this->assertStringContainsString('This value should not be blank', $content);
    }

    public function testCreateCategoryErrorDuplicateName(): void
    {

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/categories', [], [], [], json_encode([
            'name' => 'firstcat',
            'description' => 'miss name'
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $content = $client->getResponse()->getContent();

        $this->assertStringContainsString('errors', $content);
        $this->assertStringContainsString('name', $content);
        $this->assertStringContainsString('This value is already used. ', $content);
    }

    public function testUpdateCategory(): void
    {
        $client = static::createClient();
        $crawler = $client->request('PUT', '/api/categories/1', [], [], [], json_encode([
            'name' => 'first-edit',
            'description' => 'first-description',
        ]));

        $this->assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent();

        $this->assertStringContainsString('1', $content);
        $this->assertStringContainsString('first-edit', $content);
        $this->assertStringContainsString('first-description', $content);
    }

    public function testUpdateCategoryError(): void
    {
        $client = static::createClient();
        $crawler = $client->request('PUT', '/api/categories/1', [], [], [], json_encode([
            'name' => '',
            'description' => 'first-description',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $content = $client->getResponse()->getContent();

        $this->assertStringContainsString('errors', $content);
    }

    public function testAllCategories(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/categories');

        $this->assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent();

        $this->assertStringContainsString('first-edit', $content);
        $this->assertStringContainsString('secondcat', $content);
    }

    public function testDeleteCategory(): void
    {
        $client = static::createClient();
        $crawler = $client->request('DELETE', '/api/categories/1');

        $this->assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent();

        $this->assertStringContainsString('[]', $content);
    }

    public function testDeleteCategoryLinkedToProduct(): void
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/api/categories', [], [], [], json_encode([
            'name' => 'firstcatwithproduct'
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $crawler = $client->request('POST', '/api/products', [], [], [], json_encode([
            'name' => 'product for cat test',
            'price' => 1.25,
            'currency' => 'USD',
            'featured' => false,
            'category' => 3,
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $client = static::createClient();
        $crawler = $client->request('DELETE', '/api/categories/3');

        $this->assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent();

        $this->assertStringContainsString('[]', $content);
    }
}
