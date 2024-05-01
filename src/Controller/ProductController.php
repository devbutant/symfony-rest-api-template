<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'create_product', methods: ['POST'])]
    public function createProduct(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product = new Product();
        $product->setName($data['name'] ?? 'Default Name');
        $product->setDescription($data['description'] ?? 'Default Description');

        $entityManager->persist($product);
        $entityManager->flush();

        return new JsonResponse([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
        ]);
    }

    #[Route('products', name: 'get_products', methods: ['GET'])]
    public function getProducts(EntityManagerInterface $entityManager): JsonResponse
    {
        $products = $entityManager->getRepository(Product::class)->findAll();

        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/products/{id}', name: 'get_product', methods: ['GET'])]
    public function getProduct(EntityManagerInterface $entityManager, Uuid $id): JsonResponse
    {
        $product = $entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        return new JsonResponse([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
        ]);
    }

    #[Route('/products/{id}', name: 'update_product', methods: ['PATCH'])]
    public function updateProduct(Request $request, EntityManagerInterface $entityManager, Uuid $id): JsonResponse
    {
        $product = $entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $data = json_decode($request->getContent(), true);

        $product->setName($data['name'] ?? $product->getName());
        $product->setDescription($data['description'] ?? $product->getDescription());

        $entityManager->flush();

        return new JsonResponse([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
        ]);
    }

    #[Route('/products/{id}', name: 'delete_product', methods: ['DELETE'])]
    public function deleteProduct(EntityManagerInterface $entityManager, Uuid $id): JsonResponse
    {
        $product = $entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $entityManager->remove($product);
        $entityManager->flush();

        return new JsonResponse(['status' => 'Product deleted']);
    }
}
