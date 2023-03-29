<?php
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HealthCheckController extends AbstractController
{
    public function __construct(
        public EntityManagerInterface $entityManager
    ) {
    }
    public function getHttpHealthCheck()
    {
        return $this->json(['status' => 'reachable']);
    }

    public function getDatabaseHealthCheck()
    {
        return $this->json(
            ['status' => $this->entityManager->getConnection()->connect() ? 'connected' : 'dead']
        );
    }
}