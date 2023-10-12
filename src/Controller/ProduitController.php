<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Produit;
use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'app_produit')]
    public function index(): Response
    {
        return $this->render('produit/index.html.twig', [
            'controller_name' => 'ProduitController',
        ]);
    }

   #[Route('/produit/creer', name: 'app_produit_creer')]
    public function creerProduit(EntityManagerInterface $entityManager): Response
    {
        // : Response        type de retour de la méthode creerProduit
        // pour récupérer le EntityManager (manager d'entités, d'objets)
        //     on peut ajouter l'argument à la méthode comme ici  creerProduit(EntityManagerInterface $entityManager)
        //     ou on peut récupérer le EntityManager comme dans la méthode suivante

        // créer l'objet
        $produit = new Produit();
        $produit->setLibelle('haricots verts');
        $produit->setPrix(2.60);

        // dire à Doctrine que l'objet sera (éventuellement) persisté
        $entityManager->persist($produit);

        // exécuter les requêtes (indiquées avec persist) ici il s'agit de l'ordre INSERT qui sera exécuté
        $entityManager->flush();

        return new Response('Nouveau produit enregistré, son id est : '.$produit->getId());
    }

    #[Route('/produit/{id}', name: 'app_produit_lire')]
    public function lire($id, ManagerRegistry $doctrine)
    {
        // ces 2 exemples retournent le  entity manager  par défaut
        // ici nous n'utilisons qu'une base de données donc le entity manager par défaut suffit
        $entityManager = $doctrine->getManager();
        // $entityManager = $doctrine->getManager('default');
        
        // {id} dans la route permet de récupérer $id en argument de la méthode
        // on utilise le Repository de la classe Produit
        // il s'agit d'une classe qui est utilisée pour les recherches d'entités (et donc de données dans la base)
        // la classe ProduitRepository a été créée en même temps que l'entité par le make

        $produit = $entityManager
            ->getRepository(Produit::class)
            ->find($id);

        if (!$produit) {
            throw $this->createNotFoundException(
                'Ce produit n\'existe pas : ' . $id
            );
        }

       return new Response('Voici le libellé du produit : ' . $produit->getLibelle());
        // on peut bien sûr également rendre un template
    }
    #[Route('/produitautomatique/{id}', name: 'app_produitautomatique_lire')]
    public function lireautomatique(Produit $produit)
    {
        // grâce au Symfony\Bridge\Doctrine\ArgumentResolver\EntityValueResolver
        // il suffit de donner le produit en argument
        // la requête de recherche sera automatique
        // et une page 404 sera générée si le produit n'existe pas

        return new Response('Voici le libellé du produit lu automatiquement : '
            . $produit->getLibelle().
            ' crée le ' . $produit->getDataCreation()->format('Y-m-d H:i:s'));
        // on peut bien sûr également rendre un template
    }

    #[Route('/produit/modifier/{id}', name: 'app_produit_modifier')]
    public function modifier($id, EntityManagerInterface $entityManager)
    {
        // 1  recherche du produit
        $produit = $entityManager->getRepository(Produit::class)->find($id);

        // en cas de produit inexistant, affichage page 404
        if (!$produit) {
            throw $this->createNotFoundException(
                'Aucun produit avec l\'id '.$id
            );
        }

        // 2 modification des propriétés
        $produit->setLibelle('haricots verts fins');
        // 3 exécution de l'update
        $entityManager->flush();

        // redirection vers l'affichage du produit
        return $this->redirectToRoute('app_produit_lire', [
            'id' => $produit->getId()
        ]);
    }

    #[Route('/produit/supprimer/{id}', name: 'app_produit_supprimer')]
    public function supprimer($id, EntityManagerInterface $entityManager)
    {
        // 1  recherche du produit
        $produit = $entityManager->getRepository(Produit::class)->find($id);

        // en cas de produit inexistant, affichage page 404
        if (!$produit) {
            throw $this->createNotFoundException(
                'Aucun produit avec l\'id '.$id
            );
        }

        // 2 suppression du produit
        $entityManager->remove(($produit));
        // 3 exécution du delete
        $entityManager->flush();

        // affichage réponse
        return new Response('Le produit a été supprimé, id : '.$id);
    }

    #[Route('/produits/{prix}', name: 'app_produits_lireProduits')]
    public function lireProduits($prix, EntityManagerInterface $entityManager)
    {
        $produits = $entityManager
            ->getRepository(Produit::class)
            ->findAllGreaterThanPrice($prix);

        // OU
        // $repository = $entityManager->getRepository(Produit::class);
        // $produits = $repository->findAllGreaterThanPrice($prix);

        // OU
        //  ajouter :                   use App\Repository\ProduitRepository;
        // injecter le repository :      public function lireProduits($prix, ProduitRepository $repository)
        //  et écrire    :     
        //  $produits = $repository->findAllGreaterThanPrice($prix);

        return new Response('Voici le nombre de produits : '.sizeof($produits));
    }

    #[Route('/produit/complet/creer', name: 'app_produit_complet_creer')]
    public function creerProduitComplet(EntityManagerInterface $entityManager)
    {
        // créer une catégorie
        $categorie = new Categorie();
        $categorie->setLibelle('Fruits');

        // créer un produit
        $produit = new Produit();
        $produit->setLibelle('mirabelle');
        $produit->setPrix(2.50);

        // mettre en relation le produit avec la catégorie
        $produit->setCategorie($categorie);

        // persister les objets
        $entityManager->persist($categorie);
        $entityManager->persist($produit);
        // exécutez les requêtes
        $entityManager->flush();

        // retourner une réponse
        return new Response(
            'Nouveau produit enregistré avec l\'id : '.$produit->getId()
            .' et nouvelle catégorie enregistrée avec id: '.$categorie->getId()
        );
    }

}
