<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Product;
use App\Entity\User;

class ProductTest extends WebTestCase
{
    private $client;
    private $jwt_token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->login();
    }

    private function login(): void
    {
        $this->client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            "username" => "user1@productapi.com",
            "password" => "password"
        ]));

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->jwt_token = $responseContent['token'];
    }


    public function testCreateProduct(): void
    {
        $userRepository = $this->client->getContainer()->get('doctrine')->getRepository(User::class);
        $firstUser = $userRepository->findOneBy([]);
    
        $userUuid = $firstUser->getId()->toRfc4122();

        $data = [
            "name" => "zzzzzzzzzzz",
            "description" => "Souris Logitech MX Master 3",
            "owner" => "/api/users/".$userUuid
        ];
    
        $this->client->request('POST', '/api/products', [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->jwt_token
        ], json_encode($data));
    
        $this->assertResponseIsSuccessful();
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseData);
    }

    public function testGetProducts(): void
    {
        $this->client->request('GET', '/api/products', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->jwt_token
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
    }
    
    public function testGetProduct(): void
    {
        $userRepository = $this->client->getContainer()->get('doctrine')->getRepository(User::class);
        $firstUser = $userRepository->findOneBy([]);
    
        $product = new Product();
        $product->setOwner($firstUser);
        $product->setName('souris');
        $product->setDescription('Souris Logitech MX Master 3');
    
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($product);
        $entityManager->flush();
    
        $productId = $product->getId();

        $this->client->request('GET', '/api/products/' . $productId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->jwt_token
        ]);
    
        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
    }
}