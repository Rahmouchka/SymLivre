<?php

namespace App\Controller\user;
use App\Entity\LigneCommande;
use App\Entity\Commande;
use App\Service\PanierService;
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

    #[Route('/confirmer', name: 'user_commande_confirmer', methods: ['POST'])]
    public function confirmerCommande(
        PanierService $panierService,
        EntityManagerInterface $em
    ): Response {
        $panier = $panierService->getPanierComplet();
        $total = $panierService->getTotal();

        if (empty($panier)) {
            $this->addFlash('error', 'Le panier est vide.');
            return $this->redirectToRoute('user_livre_index');
        }

        $commande = new Commande();
        $commande->setDateCommande(new \DateTime());
        $commande->setPrixTotal($total);
        $commande->setStatus('PENDING');
        $commande->setModePaiement('non spécifié');

        $em->persist($commande);

        foreach ($panier as $item) {
            $ligneCommande = new LigneCommande();
            $ligneCommande->setLivre($item['livre']);
            $ligneCommande->setQuantite($item['quantite']);
            $ligneCommande->setPrixUnitaire($item['livre']->getPrix());
            $ligneCommande->setCommande($commande);

            $commande->addLigneCommande($ligneCommande);
            $em->persist($ligneCommande);
        }

        $em->flush();



        $this->addFlash('success', 'Commande créée ! Choisissez votre mode de paiement.');

        // Redirection vers page de choix du mode paiement
        return $this->redirectToRoute('mode_paiement', ['id' => $commande->getId()]);
    }
    #[Route('/paiement/{id}', name: 'mode_paiement', methods: ['GET', 'POST'])]
    public function modePaiement(PanierService $panierService, Request $request, Commande $commande, EntityManagerInterface $em): Response
    {
        $panierService->vider();

        if ($request->isMethod('POST')) {
            $modePaiement = $request->request->get('modePaiement');

            if ($modePaiement === 'livraison') {
                $commande->setModePaiement('livraison');
                $commande->setStatus('CONFIRMED');
                $em->flush();

                $this->addFlash('success', 'Commande confirmée. Paiement à la livraison.');
                return $this->redirectToRoute('user_livre_index');
            }
            else {
                $this->addFlash('error', 'Veuillez choisir "À la livraison" pour valider cette méthode.');
                return $this->redirectToRoute('mode_paiement', ['id' => $commande->getId()]);
            }
        }

        return $this->render('paiement/paiement.html.twig', [
            'commande' => $commande,
            'stripe_public_key' => $_ENV['STRIPE_PUBLIC_KEY'],
        ]);
    }
    #[Route('/mes-commandes', name: 'user_commandes')]
    public function mesCommandes(): Response
    {
        $user = $this->getUser();



        $commandes = $user->getCommandes();

        return $this->render('commandes/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }

}
