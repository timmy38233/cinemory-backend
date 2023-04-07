<?php
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HealthCheckController extends AbstractController
{
    public function __construct(
        public EntityManagerInterface $entityManager
    ) {
    }
    
    public function getHttpHealthCheck(): JsonResponse
    {
        return $this->json(['status' => 'reachable']);
    }

    public function getDatabaseHealthCheck(): JsonResponse
    {
        return $this->json(
            ['status' => $this->entityManager->getConnection()->connect() ? 'connected' : 'dead']
        );
    }
}