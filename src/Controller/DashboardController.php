<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    // Page principale avec le graphe
    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(EntityManagerInterface $em): Response
    {
        $qb = $em->createQueryBuilder();
        $qb->select(
            'l.titre AS titre',
            'l.image AS image',
            'SUM(lc.quantite) AS totalSold',
            'l.prix AS prix',
            'l.editeur AS editeur',
            'c.libelle AS categorie'
        )
            ->from('App\Entity\LigneCommande', 'lc')
            ->join('lc.livre', 'l')
            ->join('l.categorie', 'c')
            ->groupBy('l.id')
            ->orderBy('totalSold', 'DESC')
            ->setMaxResults(3);

        $topBooks = $qb->getQuery()->getResult();

        return $this->render('dashboard/dashboard.html.twig', [
            'topBooks' => $topBooks
        ]);
    }

    // Route pour renvoyer les données des ventes par catégorie
    #[Route('/dashboard/data', name: 'dashboard_data')]
    public function getCategorySalesData(EntityManagerInterface $em): JsonResponse
    {
        $qb = $em->createQueryBuilder();
        $qb->select('c.libelle AS category', 'SUM(lc.quantite) AS totalSold')
            ->from('App\Entity\LigneCommande', 'lc')
            ->join('lc.livre', 'l')
            ->join('l.categorie', 'c')
            ->groupBy('c.id')
            ->orderBy('totalSold', 'DESC');

        $results = $qb->getQuery()->getResult();

        return new JsonResponse($results);
    }
    #[Route('/dashboard/data/auteurs', name: 'dashboard_data_authors')]
    public function getAuthorSalesData(EntityManagerInterface $em): JsonResponse
    {
        $qb = $em->createQueryBuilder();
        $qb->select('l.editeur AS author', 'SUM(lc.quantite) AS totalSold')
            ->from('App\Entity\LigneCommande', 'lc')
            ->join('lc.livre', 'l')
            ->groupBy('l.editeur')
            ->orderBy('totalSold', 'DESC');

        $results = $qb->getQuery()->getResult();

        return new JsonResponse($results);
    }




}
