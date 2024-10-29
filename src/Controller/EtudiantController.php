<?php

namespace App\Controller;

use App\Entity\Etudiant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EtudiantController extends AbstractController
{

    #[Route("/etudiants", name: "etudiants")]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $etudiant = new Etudiant();

        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom');
            $prenom = $request->request->get('prenom');
            $dateNaissance = $request->request->get('dateNaissance');
            $adresse = $request->request->get('adresse');

            $etudiant->setNom($nom);
            $etudiant->setPrenom($prenom);
            $etudiant->setDateNaissance(new \DateTime('2000-01-01'));
            $etudiant->setAdresse($adresse);

            $em->persist($etudiant);
            $em->flush();

            return $this->redirectToRoute('etudiant_list');
        }


        return $this->render('etudiant/index.html.twig', [
            'etudiant' => $etudiant
        ]);
    }



    #[Route("/etudiants/list", name: "etudiant_list")]

    public function list( EntityManagerInterface $em): Response
    {
        $etudiants = $em->getRepository( Etudiant::class)->findAll();

        return $this->render('etudiant/list.html.twig', [
            'etudiants' => $etudiants,
        ]);
    }
}
