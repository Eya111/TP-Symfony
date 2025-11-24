<?php

namespace App\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Service dédié à l'exécution de requêtes SQL natives via DBAL.
 * Le but est de séparer la logique de requête du contrôleur.
 */
class DbalVoitureService
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        // La Connection DBAL est injectée ici
        $this->connection = $connection;
    }

    /**
     * Exécute une recherche avancée de voitures en utilisant le QueryBuilder de DBAL.
     *
     * @param array $criteria Les critères de recherche (prixMin, prixMax).
     * @return array Un tableau d'arrays associatifs (données brutes).
     */
    public function findByAdvancedSearchDbal(array $criteria): array
    {
        // Initialisation du QueryBuilder de DBAL
        $qb = $this->connection->createQueryBuilder();

        // Construction de la requête SELECT de base (avec JOIN)
        $qb->select('v.serie', 'v.date_mise_en_marche', 'v.prix_jour', 'm.libelle AS modele_libelle', 'm.pays AS modele_pays')
            ->from('voiture', 'v')
            ->innerJoin('v', 'modele', 'm', 'v.modele_id = m.id')
            ->orderBy('v.prix_jour', 'ASC');

        // Ajout des conditions WHERE basées sur les critères
        if (!empty($criteria['prixMin'])) {
            $qb->andWhere('v.prix_jour >= :prixMin')
                ->setParameter('prixMin', $criteria['prixMin']);
        }

        if (!empty($criteria['prixMax'])) {
            $qb->andWhere('v.prix_jour <= :prixMax')
                ->setParameter('prixMax', $criteria['prixMax']);
        }

        // Exécution et récupération des résultats bruts
        return $qb->executeQuery()->fetchAllAssociative();
    }

    /**
     * Récupère la liste de toutes les voitures avec le nom du modèle (DBAL).
     */
    public function findAllVoituresDbal(): array
    {
        $sql = '
            SELECT 
                v.serie, 
                v.date_mise_en_marche, 
                v.prix_jour, 
                m.libelle AS modele_libelle, 
                m.pays AS modele_pays
            FROM 
                voiture v
            INNER JOIN 
                modele m ON v.modele_id = m.id
            ORDER BY 
                v.date_mise_en_marche DESC
        ';
        return $this->connection->fetchAllAssociative($sql);
    }
}
