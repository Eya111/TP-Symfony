<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Form\VoitureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VoitureController extends AbstractController
{
    // Liste de toutes les voitures
    #[Route('/voitures', name: 'voitures_list')]
    public function listVoiture(EntityManagerInterface $em): Response
    {
        $voitures = $em->getRepository(Voiture::class)->findAll();

        return $this->render('voiture/listVoiture.html.twig', [
            'voitures' => $voitures
        ]);
    }

    // Ajouter une nouvelle voiture
    #[Route('/voiture/add', name: 'voiture_add')]
    public function addVoiture(Request $request, EntityManagerInterface $em): Response
    {
        $voiture = new Voiture();
        $form = $this->createForm(VoitureType::class, $voiture);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($voiture);
            $em->flush();

            $this->addFlash('success', 'Voiture ajoutée avec succès !');

            return $this->redirectToRoute('voitures_list');
        }

        return $this->render('voiture/addVoiture.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // Supprimer une voiture
    #[Route('/voiture/delete/{id}', name: 'voiture_delete')]
    public function deleteVoiture(EntityManagerInterface $em, $id): Response
    {
        $voiture = $em->getRepository(Voiture::class)->find($id);

        if ($voiture) {
            $em->remove($voiture);
            $em->flush();
            $this->addFlash('success', 'Voiture supprimée avec succès !');
        }

        return $this->redirectToRoute('voitures_list');
    }

    // Mettre à jour une voiture
    #[Route('/voiture/update/{id}', name: 'voiture_update')]
    public function updateVoiture(Request $request, EntityManagerInterface $em, $id): Response
    {
        $voiture = $em->getRepository(Voiture::class)->find($id);

        if (!$voiture) {
            throw $this->createNotFoundException('Voiture non trouvée');
        }

        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Voiture mise à jour avec succès !');

            return $this->redirectToRoute('voitures_list');
        }

        return $this->render('voiture/addVoiture.html.twig', [
            'form' => $form->createView()
        ]);
    }



    /**
     * Action d'ajout rapide de voitures pour les tests.
     * Utile pour créer des données rapidement pour tester le DQL.
     */
    #[Route('/addVoitures', name: 'voiture_add_multiple')]
    public function addVoitures(EntityManagerInterface $em): Response
    {
        // Récupérer des modèles existants pour lier les voitures
        $modeleClio = $em->getRepository(Modele::class)->findOneBy(['libelle' => 'Clio']);
        $modeleMegane = $em->getRepository(Modele::class)->findOneBy(['libelle' => 'Megane']);

        $count = 0;

        // Assurez-vous que les modèles existent avant d'ajouter les voitures
        if ($modeleClio) {
            $voiture1 = (new Voiture())
                ->setSerie('1234')
                ->setDateMiseEnMarche(new \DateTime('2025-11-25'))
                ->setPrixJour(100.00)
                ->setModele($modeleClio);
            $em->persist($voiture1);
            $count++;
        }

        if ($modeleMegane) {
            $voiture2 = (new Voiture())
                ->setSerie('5678')
                ->setDateMiseEnMarche(new \DateTime('2025-12-01'))
                ->setPrixJour(150.00)
                ->setModele($modeleMegane);
            $em->persist($voiture2);
            $count++;
        }

        $em->flush();

        return new Response("{$count} voiture(s) ajoutée(s) via /addVoitures. (Vérifiez la liste des voitures)");
    }

    /**
     * Action de filtrage des voitures par modèle (DQL simple)
     */
    #[Route('/voitures-par-modele', name: 'voiture_par_modele')]
    public function voitureParModele(Request $request, VoitureRepository $voitureRepo): Response
    {
        // 1. Création du formulaire de filtre
        $form = $this->createFormBuilder()
            ->add('modele', EntityType::class, [
                'class' => Modele::class,
                'choice_label' => 'libelle',
                'placeholder' => 'Sélectionner un modèle',
                'required' => false,
            ])
            ->add('filtrer', SubmitType::class, ['label' => 'Filtrer', 'attr' => ['class' => 'btn-primary']])
            ->getForm();

        $form->handleRequest($request);
        $voitures = [];
        $modeleSelectionne = null;

        // 2. Traitement du formulaire et exécution de la requête DQL
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $modeleSelectionne = $data['modele'];

            if ($modeleSelectionne) {
                // Appel de la méthode DQL (findByModele) dans le Repository
                $voitures = $voitureRepo->findByModele($modeleSelectionne->getId());
            }
        }

        // 3. Rendu de la vue Twig
        return $this->render('voiture/voiture_par_modele.html.twig', [
            'form' => $form->createView(),
            'voitures' => $voitures, // Liste des résultats
        ]);
    }

    /**
     * NOUVEAU: Action de recherche avancée (DQL QueryBuilder complexe)
     */
    #[Route('/recherche-avancee', name: 'recherche_avancee')]
    public function rechercheAvancee(Request $request, VoitureRepository $voitureRepo): Response
    {
        // 1. Création du formulaire de recherche avancée
        $form = $this->createFormBuilder()
            ->add('prixMin', NumberType::class, [
                'label' => 'Prix Minimum (€)',
                'required' => false,
                'html5' => true,
                'attr' => ['placeholder' => '100']
            ])
            ->add('prixMax', NumberType::class, [
                'label' => 'Prix Maximum (€)',
                'required' => false,
                'html5' => true,
                'attr' => ['placeholder' => '300']
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Mise en Marche Après',
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'Mise en Marche Avant',
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('rechercher', SubmitType::class, ['label' => 'Rechercher', 'attr' => ['class' => 'btn-success']])
            ->getForm();

        $form->handleRequest($request);
        $voitures = [];
        $criteres = [];

        // 2. Traitement du formulaire et exécution de la requête DQL avancée
        if ($form->isSubmitted() && $form->isValid()) {
            $criteres = $form->getData();

            // Appel de la méthode DQL QueryBuilder (findByAdvancedSearch) dans le Repository
            $voitures = $voitureRepo->findByAdvancedSearch($criteres);
        }

        // 3. Rendu de la vue Twig
        return $this->render('voiture/recherche_avancee.html.twig', [
            'form' => $form->createView(),
            'voitures' => $voitures, // Liste des résultats
            'criteres' => $criteres, // Les critères pour l'affichage
        ]);
    }



}
