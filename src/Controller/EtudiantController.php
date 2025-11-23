<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EtudiantController extends AbstractController
{
    #[Route('/etudiant', name: 'etudiant')]
    public function index(): Response
    {
        return new Response("Bienvenue dans la page Etudiant !");
    }

    #[Route('/affichage_etudiant/{id}', name: 'affichage_etudiant', requirements: ['id' => '\d{2}'])]
    public function affichageEtudiant(int $id): Response
    {
        return new Response("L'ID de l'étudiant est : $id");
    }

    #[Route('/voirNom/{name}', name: 'etudiant_name')]
    public function voirNom(string $name): Response
    {
        return $this->render('etudiants/etudiant.html.twig', [
            'name' => $name
        ]);
    }
    #[Route('/list', name: 'liste')]
    public function listEtudiant(): Response
    {
        $modules = [
            ['nom' => 'Mathématiques', 'code' => 'MAT101'],
            ['nom' => 'Physique', 'code' => 'PHY101'],
            ['nom' => 'Informatique', 'code' => 'INF101']
        ];

        return $this->render('etudiants/list.html.twig', [
            'modules' => $modules
        ]);
    }
    #[Route('/affecter', name: 'affecter')]
    public function affecter(): Response
    {
        return $this->render('etudiants/affecter.html.twig', [
            'message' => "Page d'affectation des étudiants"
        ]);
    }
    #[Route('/index-fils', name: 'index_fils')]
    public function indexFils(): Response
    {
        $modules = [
            ['nom' => 'Mathématiques', 'code' => 'MAT101'],
            ['nom' => 'Physique', 'code' => 'PHY101'],
            ['nom' => 'Informatique', 'code' => 'INF101']
        ];

        return $this->render('etudiants/index.html.twig', [
            'modules' => $modules
        ]);
    }


}


