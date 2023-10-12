<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Categorie;
use App\Entity\Produit;
use App\Entity\Recette;

class ProduitFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $tbDataRecettes = ['ratatouille', 'clafoutis', 'tarte aux pommes', 'gratin 
        dauphinois', 'salade Caesar', 'potée lorraine'];
        

        $tbDataProduits = [
            [ 'categorie' => 'Fruits',
            'produits' => [
                [ 'libelle' => 'mirabelle', 'prix' => 2.50  ] ,
                [ 'libelle' => 'pomme', 'prix' => 2.30  ] ,
                [ 'libelle' => 'poire', 'prix' => 2.70  ] ,
                [ 'libelle' => 'cerise', 'prix' => 3.30  ] ,
            ] ],
            [ 'categorie' => 'Aromatiques',
            'produits' => [
                [ 'libelle' => 'basilic', 'prix' => 1.00  ] ,
                [ 'libelle' => 'romarin', 'prix' => 1.00  ] ,
                [ 'libelle' => 'persil', 'prix' => 1.00  ] ,
                [ 'libelle' => 'menthe', 'prix' => 1.00  ] ,
                [ 'libelle' => 'coriandre', 'prix' => 1.00  ] ,
            ] ],
            [ 'categorie' => 'Légumes',
                  'produits' => [
                [ 'libelle' => 'courgette', 'prix' => 2.50  ] ,
                [ 'libelle' => 'aubergine', 'prix' => 2.30  ] ,
                [ 'libelle' => 'laitue', 'prix' => 1.10  ] ,
                [ 'libelle' => 'carotte', 'prix' => 1.30  ] ,
                [ 'libelle' => 'brocoli', 'prix' => 2.30  ] ,
                [ 'libelle' => 'pomme de terre', 'prix' => 2.70  ] ,
                [ 'libelle' => 'chou rouge', 'prix' => 1.30  ] ,
            ] ],
            [ 'categorie' => 'Confitures',
                'produits' => [
                    [ 'libelle' => 'mirabelle', 'prix' => 2.50  ] ,
                    [ 'libelle' => 'fraise', 'prix' => 2.30  ] ,
                    [ 'libelle' => 'framboise', 'prix' => 2.70  ] ,
                    [ 'libelle' => 'cerise', 'prix' => 3.30  ] ,
                ] ],
            [ 'categorie' => 'Miels',
                'produits' => [
                    [ 'libelle' => 'acacia', 'prix' => 2.50  ] ,
                    [ 'libelle' => 'sapin', 'prix' => 2.30  ] ,
                    [ 'libelle' => 'montagne', 'prix' => 2.70  ] ,
            ] ],
        ];
        // créer les recettes
        $lesRecettes = array();
        for ($i = 0; $i < count($tbDataRecettes); $i++) {
            $recette = new Recette();
            $recette->setNom($tbDataRecettes[$i]);
            $lesRecettes[$i] = $recette;
            $manager->persist($recette);
        }

        // créer les produits, leurs catégories et utiliser  aléatoirement des produits dans les recettes

        for($i = 0; $i < count($tbDataProduits); ++$i) {
            // créer une catégorie
            $categorie = new Categorie();
            $categorie->setLibelle($tbDataProduits [$i] ['categorie']);
            $manager->persist($categorie);
            // créer les produits de la catégorie
            foreach ($tbDataProduits [$i] ['produits'] as $unProduit) {
                $produit = new Produit();
                $produit->setLibelle($unProduit['libelle']);
                $produit->setPrix($unProduit['prix']);
                // mettre en relation le produit avec la catégorie
                $produit->setCategorie($categorie);
                
                                // utiliser le produit dans des recettes de façon aléatoire
                //      au hasard, nombre N de recettes auxquelles le produit sera ajouté
                $nbRecettes = rand(0, count($lesRecettes) - 1);
                if ($nbRecettes > 0) {
                    //  au tire au hasard N recettes
                    $randRecettes = array_rand($lesRecettes, $nbRecettes);
                    //  on ajoute le produit à chacune des N recettes tirée au hasard
                    if ( is_array($randRecettes)) {
                        for ($j = 0; $j < count($randRecettes); $j++) {
                            $uneRecette = $lesRecettes[$randRecettes[$j]];
                            $uneRecette->addProduit($produit);
                        }
                    }
                    else{
                        $uneRecette = $lesRecettes[$randRecettes];
                        $uneRecette->addProduit($produit);
                    }
                }

                $manager->persist($produit);
            }
        }

        // exécuter les mises à jour de la base de données

       
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
