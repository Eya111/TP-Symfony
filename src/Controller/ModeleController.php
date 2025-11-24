<?php

namespace App\Controller;

use App\Entity\Modele;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModeleController extends AbstractController
{
    /**
     * Action pour afficher la liste des modèles.
     */
    #[Route('/modeles', name: 'modeles_list')]
    public function listModeles(EntityManagerInterface $em): Response
    {
        $modeles = $em->getRepository(Modele::class)->findAll();

        // On renvoie une réponse simple (dans un TP réel, ce serait un template Twig)
        $output = "<h1>Liste des Modèles (pour les tests DQL)</h1>";
        if (empty($modeles)) {
            $output .= "<p>Aucun modèle trouvé. <a href=\"/modele/add-dql\">Ajouter un modèle avec DQL</a></p>";
        } else {
            $output .= "<ul>";
            foreach ($modeles as $modele) {
                $output .= "<li>ID: {$modele->getId()}, Libellé: {$modele->getLibelle()}, Pays: {$modele->getPays()}</li>";
            }
            $output .= "</ul>";
        }

        $output .= "<h2>Actions DQL :</h2>";
        $output .= "<ul>";
        $output .= "<li><a href=\"/modele/add-dql\">1. Ajouter un modèle (DQL d'Insertion)</a></li>";
        $output .= "<li><a href=\"/modele/update-dql\">2. Mettre à jour les modèles (DQL de Mise à Jour)</a></li>";
        $output .= "<li><a href=\"/modele/delete-dql\">3. Supprimer un modèle (DQL de Suppression)</a></li>";
        $output .= "</ul>";

        return new Response($output);
    }

    /**
     * 1. DQL d'Insertion (Page 11 du TP)
     * Ajout d'un nouveau modèle en utilisant l'approche Entité (car DQL d'INSERT n'est pas supporté par ORM).
     */
    #[Route('/modele/add-dql', name: 'modele_add_dql')]
    public function addModeleDQL(EntityManagerInterface $em): Response
    {
        // Simuler l'insertion DQL en utilisant l'approche Entité recommandée par Doctrine
        $modele = new Modele();
        $modele->setLibelle("DQL Test " . rand(1, 100));
        $modele->setPays("Tunisie");

        $em->persist($modele);
        $em->flush();

        return $this->redirectToRoute('modeles_list', [], 302);
    }

    /**
     * Met à jour le pays de tous les modèles "DQL Test" vers "France".
     */
    #[Route('/modele/update-dql', name: 'modele_update_dql')]
    public function updateModeleDQL(EntityManagerInterface $em): Response
    {
        // DQL de Mise à Jour
        $query = $em->createQuery(
            'UPDATE App\Entity\Modele m
            SET m.pays = :nouveauPays
            WHERE m.pays = :ancienPays'
        );
        $query->setParameter('nouveauPays', 'France');
        $query->setParameter('ancienPays', 'Tunisie');

        $rows = $query->execute(); // Exécute la requête DQL d'UPDATE

        return new Response("Modèles mis à jour : {$rows} ligne(s). <a href=\"/modeles\">Retour à la liste</a>");
    }

    /**
     * Supprime tous les modèles dont le libellé commence par "DQL Test".
     */
    #[Route('/modele/delete-dql', name: 'modele_delete_dql')]
    public function deleteModeleDQL(EntityManagerInterface $em): Response
    {
        // DQL de Suppression
        $query = $em->createQuery(
            'DELETE FROM App\Entity\Modele m
            WHERE m.libelle LIKE :libellePrefixe'
        );
        // On utilise la fonction LIKE de DQL
        $query->setParameter('libellePrefixe', 'DQL Test%');

        $rows = $query->execute(); // Exécute la requête DQL de DELETE

        return new Response("Modèles supprimés : {$rows} ligne(s). <a href=\"/modeles\">Retour à la liste</a>");
    }
}
