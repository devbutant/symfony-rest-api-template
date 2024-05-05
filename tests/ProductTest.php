<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Product;
use App\Entity\User;

class ProductTest extends WebTestCase
{
    public function testCreateProduct(): void
    {
        $client = static::createClient();
        $client->request('POST', '/products', [], [], [], json_encode([
            "owner_id" => "018f49ca-08bb-75a8-bea9-160b7d5217a4",
            "name" => "souris",
            "description" => "Souris Logitech MX Master 3"
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
        $product->setOwner($client->getContainer()->get('doctrine')->getRepository(User::class)->find('018f49ca-08bb-75a8-bea9-160b7d5217a4'));
        $product->setName('souris');
        $product->setDescription('Souris Logitech MX Master 3');
    
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($product);
        $entityManager->flush();
    
        $productId = $product->getId();

        $client->request('GET', '/products/' . $productId);
    
        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
    }
}