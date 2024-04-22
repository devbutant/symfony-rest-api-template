<?php

namespace App\Tests;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductTest extends WebTestCase
{
    public function testSomething(): void
    {
        $this->assertTrue(true);
    }

    public function testShowProduct(): void
    {
        $client = static::createClient();
        $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();

    
        // Création d'un produit de test
        $product = new Product();
        $productName = "Test Product Name";
        $productDescription = "Test Description";

        $product->setName($productName);
        $product->setDescription($productDescription);
    
        $entityManager->persist($product);
        $entityManager->flush();
        // Récupération de l'ID du produit
        $productId = $product->getId();

        // Requête GET pour afficher le produit en utilisant l'ID du produit
        $client->request('GET', '/product/'.$productId);

        // Vérification de la réussite de la requête
        $responseContent = $client->getResponse()->getContent();
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString($productName, $responseContent);
    }
}
