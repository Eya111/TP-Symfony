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
}
