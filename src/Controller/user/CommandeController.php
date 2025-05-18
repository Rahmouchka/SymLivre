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
    /*
    #[Route('/user/panier/ajouter/{id}', name: 'panier_ajouter')]
    public function ajouterLivre(PanierService $panierService, int $id): Response
    {
        $panierService->ajouter($id);

        $this->addFlash('success', 'Livre ajouté au panier.');

        return $this->redirectToRoute('user_livre_index');
    }
    #[Route('/user/panier', name: 'panier_afficher')]
    public function afficherPanier(PanierService $panierService): Response
    {
        $panierComplet = $panierService->getPanierComplet();
        $total = $panierService->getTotal();

        return $this->render('user/panier/afficher.html.twig', [
            'panier' => $panierComplet,
            'total' => $total,
        ]);
    }

    #[Route('/user/commande/confirmer', name: 'user_commande_confirmer', methods: ['POST'])]
    public function confirmerCommande(
        PanierService $panierService,
        EntityManagerInterface $em
    ): Response {
        $panier = $panierService->getPanierComplet();
        $total = $panierService->getTotal();

        // Exemple de persistance d'une commande
        $commande = new Commande();
        $commande->setDateCommande(new \DateTime());
        $commande->setPrixTotal($total);
        $commande->setStatus('PENDING');

        foreach ($panier as $item) {
            $ligneCommande = new LigneCommande();
            $ligneCommande->setLivre($item['livre']);
            $ligneCommande->setQuantite($item['quantite']);
            $ligneCommande->setPrixUnitaire($item['livre']->getPrix());

            $commande->addLigneCommande($ligneCommande);
            $em->persist($ligneCommande);
        }

        $em->persist($commande);
        $em->flush();

        // Vider le panier
        $panierService->vider();

        return $this->redirectToRoute('user_commande_confirmation', [
            'id' => $commande->getId()
        ]);
    }

    #[Route('/user/commande/confirmation/{id}', name: 'user_commande_confirmation', methods: ['GET'])]
    public function afficherConfirmation(Commande $commande): Response
    {
        return $this->render('user/commande/confirmation.html.twig', [
            'commande' => $commande,
        ]);
    }
    */
}
