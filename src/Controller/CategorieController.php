<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class CategorieController extends AbstractController
{
    #[Route('/categorie', name: 'app_categorie')]
    #[Route('/categorie/demandermodification/{id<\d+>}', name: 'app_categorie_demandermodification')]
    public function index(CategorieRepository $repository,Request $request,  $id = null): Response
    {
        // créer l'objet et le formulaire de création
        $categorie = new Categorie();
        $formCreation = $this->createForm(CategorieType::class, $categorie);
 
        // si 2e route alors $id est renseigné et on  crée le formulaire de modification
    $formModificationView = null;
    if ($id != null) {
        // sécurité supplémentaire, on vérifie le token
        if ($this->isCsrfTokenValid('action-item'.$id, $request->get('_token'))) {
            $categorieModif = $repository->find($id);   // la catégorie à modifier
            $formModificationView = $this->createForm(CategorieType::class, $categorieModif)->createView();
        }
    }
      

        // lire les catégories
        $lesCategories = $repository->findAll();

        return $this->render('categorie/index.html.twig', [
            'formCreation' => $formCreation->createView(),
            'lesCategories' => $lesCategories,
            'formModification' => $formModificationView,
            'idCategorieModif' => $id,

        ]);
    }

    #[Route('/categorie/ajouter', name: 'app_categorie_ajouter')]
    public function ajouter(Categorie $categorie = null, Request $request, EntityManagerInterface $entityManager, CategorieRepository $repository)
    {
        //  $categorie objet de la classe Categorie, il contiendra les valeurs saisies dans les champs après soumission du formulaire.
        //  $request  objet avec les informations de la requête HTTP (GET, POST, ...)
        //  $entityManager  pour la persistance des données
    
        // création d'un formulaire de type CategorieType
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
    
        // handleRequest met à jour le formulaire
        //  si le formulaire a été soumis, handleRequest renseigne les propriétés
        //      avec les données saisies par l'utilisateur et retournées par la soumission du formulaire
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // c'est le cas du retour du formulaire
            //         l'objet $categorie a été automatiquement "hydraté" par Doctrine
            // dire à Doctrine que l'objet sera (éventuellement) persisté
            $entityManager->persist($categorie);
            // exécuter les requêtes (indiquées avec persist) ici il s'agit de l'ordre INSERT qui sera exécuté
            $entityManager->flush();
            // ajouter un message flash de succès pour informer l'utilisateur
            $this->addFlash(
                'success',
                'La catégorie ' . $categorie->getLibelle() . ' a été ajoutée.'
            );
            // rediriger vers l'affichage des catégories qui comprend le formulaire pour l"ajout d'une nouvelle catégorie
            return $this->redirectToRoute('app_categorie');
    
        } else {
    // affichage de la liste des catégories avec le formulaire de création et ses erreurs
            // lire les catégories
            $lesCategories = $repository->findAll();
            // rendre la vue
            return $this->render('categorie/index.html.twig', [
                'formCreation' => $form->createView(),
                'lesCategories' => $lesCategories,
                'formModification' => null,
                'idCategorieModif' => null,
            ]);
        }
    }

    #[Route('/categorie/modifier/{id<\d+>}', name: 'app_categorie_modifier')]
public function modifier(Categorie $categorie = null, $id = null, Request $request, EntityManagerInterface $entityManager, CategorieRepository $repository)
{
    //  Symfony 4 est capable de retrouver la catégorie à l'aide de Doctrine ORM directement en utilisant l'id passé dans la route
    $form = $this->createForm(CategorieType::class, $categorie);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        // va effectuer la requête d'UPDATE en base de données
        // pas besoin de "persister" l'entité car l'objet a déjà été retrouvé à partir de Doctrine ORM.
        $entityManager->flush();
        $this->addFlash(
            'success',
            'La catégorie '.$categorie->getLibelle().' a été modifiée.'
        );
        // rediriger vers l'affichage des catégories qui comprend le formulaire pour l"ajout d'une nouvelle catégorie
        return $this->redirectToRoute('app_categorie');

    } else {
        // affichage de la liste des catégories avec le formulaire de modification et ses erreurs
        // créer l'objet et le formulaire de création
        $categorie = new Categorie();
        $formCreation = $this->createForm(CategorieType::class, $categorie);
        // lire les catégories
        $lesCategories = $repository->findAll();
        // rendre la vue
        return $this->render('categorie/index.html.twig', [
            'formCreation' => $formCreation->createView(),
            'lesCategories' => $lesCategories,
            'formModification' => $form->createView(),
            'idCategorieModif' => $categorie->getId(),
        ]);
    }
}

#[Route('/categorie/supprimer/{id<\d+>}', name: 'app_categorie_supprimer')]
public function supprimer(Categorie $categorie = null, Request $request, EntityManagerInterface $entityManager)
{
     // vérifier le token
    if ($this->isCsrfTokenValid('action-item'.$categorie->getId(), $request->get('_token'))) {
        if ($categorie->getProduits()->count() > 0) {
            $this->addFlash(
                'error',
                'Il existe des produits dans la catégorie ' . $categorie->getLibelle() . ', elle ne peut pas être supprimée.'
            );
            return $this->redirectToRoute('app_categorie');
        }
        // supprimer la catégorie
        $entityManager->remove($categorie);
        $entityManager->flush();
        $this->addFlash(
            'success',
            'La catégorie ' . $categorie->getLibelle() . ' a été supprimée.'
        );
    }
    return $this->redirectToRoute('app_categorie');
}



}
