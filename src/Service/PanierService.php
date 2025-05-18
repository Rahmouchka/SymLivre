<?php

namespace App\Service;

use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PanierService
{
    private $requestStack;
    private $livreRepository;
    private $em;

    public function __construct(
        RequestStack $requestStack,
        LivreRepository $livreRepository,
        EntityManagerInterface $em
    ) {
        $this->requestStack = $requestStack;
        $this->livreRepository = $livreRepository;
        $this->em = $em;
    }

    private function getSession()
    {
        return $this->requestStack->getSession();
    }

    public function ajouter(int $livreId): void
    {
        $session = $this->getSession();
        $panier = $session->get('panier', []);

        // Vérifier si le livre est en stock
        $livre = $this->livreRepository->find($livreId);
        if (!$livre || $livre->getQteEnStock() <= 0) {
            throw new \Exception('Ce livre n\'est pas disponible');
        }

        if (!empty($panier[$livreId])) {
            $panier[$livreId]++;
        } else {
            $panier[$livreId] = 1;
        }

        $session->set('panier', $panier);
    }

    public function retirer(int $livreId): void
    {
        $panier = $this->getPanierComplet();

        if (isset($panier[$livreId])) {
            if ($panier[$livreId]['quantite'] > 1) {
                $panier[$livreId]['quantite']--;
            } else {
                unset($panier[$livreId]);
            }
            $this->savePanier($panier);
        }
    }

    public function supprimer(int $livreId): void
    {
        $panier = $this->getPanierComplet();

        if (isset($panier[$livreId])) {
            unset($panier[$livreId]);
            $this->savePanier($panier);
        }
    }

    public function vider(): void
    {
        $this->getSession()->remove('panier');
    }

    public function getPanierComplet(): array
    {
        $sessionPanier = $this->getSession()->get('panier', []);
        $panier = [];

        foreach ($sessionPanier as $livreId => $quantite) {
            $livre = $this->livreRepository->find($livreId);
            if ($livre) {
                $panier[$livreId] = [
                    'livre' => $livre,
                    'quantite' => $quantite
                ];
            }
        }

        return $panier;
    }

    public function getTotal(): float
    {
        $total = 0;

        foreach ($this->getPanierComplet() as $item) {
            $total += $item['livre']->getPrix() * $item['quantite'];
        }

        return $total;
    }

    public function getNombreItems(): int
    {
        return array_sum($this->getSession()->get('panier', []));
    }

    private function savePanier(array $panier): void
    {
        $simplePanier = [];
        foreach ($panier as $livreId => $item) {
            $simplePanier[$livreId] = $item['quantite'];
        }
        $this->getSession()->set('panier', $simplePanier);
    }
}