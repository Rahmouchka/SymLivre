<?php

namespace App\Controller\user;

use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/panier')]
class PanierController extends AbstractController
{
    #[Route('/', name: 'panier_afficher')]
    public function index(PanierService $panierService): Response
    {
        return $this->render('user/panier/index.html.twig', [
            'panier' => $panierService->getPanierComplet(),
            'total' => $panierService->getTotal()
        ]);
    }

    #[Route('/ajouter/{id}', name: 'panier_ajouter')]
    public function ajouter(int $id, PanierService $panierService): Response
    {
        try {
            $panierService->ajouter($id);
            $this->addFlash('success', 'Livre ajouté au panier');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('panier_afficher');
    }

    #[Route('/retirer/{id}', name: 'panier_retirer')]
    public function retirer(int $id, PanierService $panierService): Response
    {
        $panierService->retirer($id);
        $this->addFlash('success', 'Quantité mise à jour');
        return $this->redirectToRoute('panier_afficher');
    }

    #[Route('/supprimer/{id}', name: 'panier_supprimer')]
    public function supprimer(int $id, PanierService $panierService): Response
    {
        $panierService->supprimer($id);
        $this->addFlash('success', 'Livre retiré du panier');
        return $this->redirectToRoute('panier_afficher');
    }

    #[Route('/vider', name: 'panier_vider')]
    public function vider(PanierService $panierService): Response
    {
        $panierService->vider();
        $this->addFlash('success', 'Panier vidé');
        return $this->redirectToRoute('panier_afficher');
    }
}