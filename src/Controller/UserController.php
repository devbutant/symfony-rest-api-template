<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    #[Route('/api/users', name: 'get_all_users', methods: ['GET'])]
    public function get_users(EntityManagerInterface $entityManager): JsonResponse
    {
        $users = $entityManager->getRepository(User::class)->findAll();
        $data = [];
        
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'pseudo' => $user->getPseudo(),
                'bio' => $user->getBio(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/api/users/{id}', name: 'get_user', methods: ['GET'])]
    public function get_user(EntityManagerInterface $entityManager, string $id): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        return new JsonResponse([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'pseudo' => $user->getPseudo(),
            'bio' => $user->getBio(),
        ]);
    }

    #[Route('/api/users', name: 'create_user', methods: ['POST'])]
    public function create_user(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $passwordHashed = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword($passwordHashed);
        $user->setPseudo($data['pseudo']);
        $user->setBio($data['bio']);

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'pseudo' => $user->getPseudo(),
            'bio' => $user->getBio(),
        ]);
    }

    #[Route('/api/users/{id}', name: 'update_user', methods: ['PATCH'])]
    public function update_user(Request $request, EntityManagerInterface $entityManager, string $id): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
    
        $data = json_decode($request->getContent(), true);

        if (isset($data['email']) && !empty($data['email'])) {
            $user->setEmail($data['email']);
        }
    
        if (isset($data['password']) && !empty($data['password'])) {
            $user->setPassword($data['password']);
        }
    
        if (isset($data['pseudo']) && !empty($data['pseudo'])) {
            $user->setPseudo($data['pseudo']);
        }
    
        if (isset($data['bio']) && !empty($data['bio'])) {
            $user->setBio($data['bio']);
        }
    
        $entityManager->flush();
    
        return new JsonResponse([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'pseudo' => $user->getPseudo(),
            'bio' => $user->getBio(),
        ]);
    }

    #[Route('/api/users/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function delete_user(EntityManagerInterface $entityManager, string $id): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'User deleted']);
    }
}
