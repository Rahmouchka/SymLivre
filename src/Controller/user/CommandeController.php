<?php

namespace App\Controller\user;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('user/commande')]
final class CommandeController extends AbstractController
{
    #[Route(name: 'user_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('user/commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'user_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('user/commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }
}
