<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Product;

class ProductTest extends WebTestCase
{
    public function testCreateProduct(): void
    {
        $client = static::createClient();
        $client->request('POST', '/products', [], [], [], json_encode([
            'name' => 'Test Product',
            'description' => 'Test Description',
        ]));

        $this->assertResponseIsSuccessful();
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseData);
    }

    public function testGetProducts(): void
    {
        $client = static::createClient();
        $client->request('GET', '/products');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
    }


    
    public function testGetProduct(): void
    {
        $client = static::createClient();

        $product = new Product();
        $product->setName('Test Product');
        $product->setDescription('Test Description');

        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($product);
        $entityManager->flush();

        $productId = $product->getId();

        $client->request('GET', '/products/' . $productId);

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
    }
}