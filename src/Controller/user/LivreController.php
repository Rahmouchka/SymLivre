<?php

namespace App\Controller\user;

use App\Entity\Livre;
use App\Repository\LivreRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user/livre')]
final class LivreController extends AbstractController
{
    #[Route(name: 'user_livre_index', methods: ['GET'])]
    function index(LivreRepository $livreRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $searchTerm = $request->query->get('search');

        $query = $livreRepository->findBySearchQuery($searchTerm);

        $livres = $paginator->paginate(
            $query, /* Résultats à paginer */
            $request->query->getInt('page', 1), /* Numéro de page */
            12 /* Limite par page */
        );

        return $this->render('user/livre/index.html.twig', [
            'livres' => $livres,
            'searchTerm' => $searchTerm,
        ]);
    }

    #[Route('/{id}', name: 'user_livre_show', methods: ['GET'])]
    public function show(Livre $livre, LivreRepository $livreRepository): Response
    {
        // Récupérer les livres de la même catégorie (similaires) en excluant le livre actuel
        $similarBooks = [];
        if ($livre->getCategorie()) {
            $similarBooks = $livreRepository->createQueryBuilder('l')
                ->where('l.categorie = :categorie')
                ->andWhere('l.id != :currentId')
                ->setParameter('categorie', $livre->getCategorie())
                ->setParameter('currentId', $livre->getId())
                ->orderBy('l.id', 'DESC')
                ->setMaxResults(4)
                ->getQuery()
                ->getResult();
        }

        return $this->render('user/livre/show.html.twig', [
            'livre' => $livre,
            'similarBooks' => $similarBooks,
        ]);
    }

}
