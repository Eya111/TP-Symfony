<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection; // Import obligatoire pour l'accès au DBAL

class DbalController extends AbstractController
{
    /**
     * Affiche des statistiques simples en utilisant des requêtes SQL natives via DBAL.
     * La dépendance `Connection $connection` est injectée par Symfony.
     */
    #[Route('/stats-dbal', name: 'dbal_stats')]
    public function showStats(Connection $connection): Response
    {
        // 1. Requête SQL pour compter les Voitures
        // On utilise la méthode fetchOne() pour récupérer le résultat d'une seule colonne/ligne.
        $sqlVoiture = 'SELECT COUNT(id) FROM voiture';
        $countVoitures = $connection->fetchOne($sqlVoiture);

        // 2. Requête SQL pour compter les Modèles
        $sqlModele = 'SELECT COUNT(id) FROM modele';
        $countModeles = $connection->fetchOne($sqlModele);

        // 3. Rendu du template Twig
        return $this->render('dbal/statsDbal.html.twig', [
            'countVoitures' => $countVoitures,
            'countModeles' => $countModeles,
        ]);
    }

    /**
     * PROCHAINE ÉTAPE : Implémenter une recherche avec JOIN en SQL natif.
     */
    // #[Route('/dbal/recherche', name: 'dbal_recherche')]
    // public function rechercheAvanceeDbal(Connection $connection): Response
    // {
    //     // LOGIQUE SQL COMPLEXE ICI
    // }
}