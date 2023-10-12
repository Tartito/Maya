<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use App\Entity\Produit;
use App\Entity\Recette;
use Doctrine\ORM\EntityManagerInterface;


class RecetteController extends AbstractController
{
    #[Route('/recette', name: 'app_recette')]
    public function index(): Response
    {
        return $this->render('recette/index.html.twig', [
            'controller_name' => 'RecetteController',
        ]);
    }

    #[Route('/recette/creer', name: 'app_recette_creer')]
    public function creerRecette(EntityManagerInterface $entityManager): Response
    {
        // créer l'objet Recette
        $recette = new Recette();
        $recette->setNom('ratatouille');

        // chercher l'id du produit 'aubergine' et l'ajouter à la collection de produits de la recette
        $produit = $entityManager
            ->getRepository(Produit::class)
            ->findOneBy(['libelle' => 'aubergine']);
        $recette->addProduit($produit);

        // chercher l'id du produit 'courgette' et l'ajouter à la collection de produits de la recette
        $produit = $entityManager
            ->getRepository(Produit::class)
            ->findOneBy(['libelle' => 'courgettes']);
        $recette->addProduit($produit);

        // dire à Doctrine que l'objet sera (éventuellement) persisté
        $entityManager->persist($recette);

        // exécuter les requêtes (indiquées avec persist) ici il s'agit d'ordres INSERT qui seront exécutés
        $entityManager->flush();

        return new Response('Nouvelle recette enregistrée avec 2 produits, son id est : '.$recette->getId());
    }


}
