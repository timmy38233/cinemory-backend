<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{

    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder,
        private EntityManagerInterface      $entityManager
    )
    {
    }

    public function register(Request $request): JsonResponse
    {
        try {
            $jsonData = json_decode($request->getContent(), true, flags: JSON_THROW_ON_ERROR);

            $user = (new User())
                ->setUsername($jsonData['username'])
                ->setFirstName($jsonData['firstName'] ?? '')
                ->setLastName($jsonData['lastName'] ?? '')
                ->setEmail($jsonData['email']);

            $hashedPassword = $this->passwordEncoder->hashPassword($user, $jsonData['password']);
            $user->setPassword($hashedPassword);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
        catch (\JsonException $e)
        {
            return $this->json([
                'status' => 'error',
                'error' => 'invalid_json',
                'message' => $e->getMessage()
            ], 400);
        }
        catch (UniqueConstraintViolationException $e)
        {
            return $this->json([
                'status' => 'error',
                'error' => 'invalid_user',
                'message' => $e->getMessage()
            ], 400);
        }

        return $this->json([
            'status' => 'success',
            'success' => 'registered_user',
            'userIdentifier' => $user->getUserIdentifier()
        ]);
    }
}
