<?php

namespace App\Tests;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryTest extends WebTestCase
{

    public function testCreateCategory(): void
    {

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/category', [], [], [], json_encode([
            'name' => 'firstcat'
        ]));

        $this->assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent();

        $this->assertStringContainsString('firstcat', $content);

        $crawler = $client->request('POST', '/api/category', [], [], [], json_encode([
            'name' => 'secondcat'
        ]));

        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();

        $this->assertStringContainsString('secondcat', $content);
    }

    public function testUpdateCategory(): void
    {
        $client = static::createClient();
        $crawler = $client->request('PUT', '/api/category/1', [], [], [], json_encode([
            'name' => 'first-edit',
            'description' => 'first-description',
        ]));

        $this->assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent();

        $this->assertStringContainsString('1', $content);
        $this->assertStringContainsString('first-edit', $content);
        $this->assertStringContainsString('first-description', $content);
    }

    public function testAllCategories(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/category');

        $this->assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent();

        $this->assertStringContainsString('first-edit', $content);
        $this->assertStringContainsString('secondcat', $content);
    }

    public function testDeleteCategory(): void
    {
        $client = static::createClient();
        $crawler = $client->request('DELETE', '/api/category/1');

        $this->assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent();

        $this->assertStringContainsString('[]', $content);
    }
}
