<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'get_all_products', methods: ['GET'])]
    public function get_products(EntityManagerInterface $entityManager): JsonResponse
    {
        $products = $entityManager->getRepository(Product::class)->findAll();
        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'owner_id' => $product->getOwner()->getId(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/api/products/{id}', name: 'get_product', methods: ['GET'])]
    public function get_product(EntityManagerInterface $entityManager, string $id): JsonResponse
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        return new JsonResponse([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'owner_id' => $product->getOwner()->getId(),
        ]);
    }

    #[Route('/api/products', name: 'create_product', methods: ['POST'])]
    public function create_product(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product = new Product();
        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setOwner($entityManager->getRepository(User::class)->find($data['owner_id']));

        $entityManager->persist($product);
        $entityManager->flush();

        return new JsonResponse([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'owner_id' => $product->getOwner()->getId(),
        ]);
    }

    #[Route('/api/products/{id}', name: 'update_product', methods: ['PATCH'])]
    public function update_product(Request $request, EntityManagerInterface $entityManager, string $id): JsonResponse
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setOwner($entityManager->getRepository(User::class)->find($data['owner_id']));

        $entityManager->persist($product);
        $entityManager->flush();

        return new JsonResponse([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'owner_id' => $product->getOwner()->getId(),
        ]);
    }

    #[Route('/api/products/{id}', name: 'delete_product', methods: ['DELETE'])]
    public function delete_product(EntityManagerInterface $entityManager, string $id): JsonResponse
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        $entityManager->remove($product);
        $entityManager->flush();

        return new JsonResponse(['status' => 'Product deleted']);
    }
}
