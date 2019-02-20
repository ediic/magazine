<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UploadsRepository;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(UploadsRepository $uploadsRepository)
    {
        return $this->render('home/index.html.twig', [
            'uploads' => $uploadsRepository->findAll(),
        ]);
    }
}
