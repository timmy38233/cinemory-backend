<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HealthCheckController extends AbstractController
{
    public function getHealthCheck()
    {
        return $this->json(['status' => "I'm alive"]);
    }
}