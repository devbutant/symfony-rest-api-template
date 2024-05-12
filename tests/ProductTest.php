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
        $userRepository = $client->getContainer()->get('doctrine')->getRepository(User::class);
        $firstUser = $userRepository->findOneBy([]);
    
        $userUuid = $firstUser->getId()->toRfc4122();

        $data = [
            "name" => "souris",
            "description" => "Souris Logitech MX Master 3",
            "owner" => "/api/users/".$userUuid
        ];
    
        $client->request('POST', '/api/products', [], [], ['CONTENT_TYPE' => 'application/ld+json'], json_encode($data));
    
        $this->assertResponseIsSuccessful();
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseData);
    }

    public function testGetProducts(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/products');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
    }
    
    public function testGetProduct(): void
    {
        $client = static::createClient();
        $userRepository = $client->getContainer()->get('doctrine')->getRepository(User::class);
        $firstUser = $userRepository->findOneBy([]);
    
        $product = new Product();
        $product->setOwner($firstUser);
        $product->setName('souris');
        $product->setDescription('Souris Logitech MX Master 3');
    
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($product);
        $entityManager->flush();
    
        $productId = $product->getId();

        $client->request('GET', '/api/products/' . $productId);
    
        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
    }
}