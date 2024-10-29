<?php

namespace App\Controller;

use App\Entity\Filiere;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FiliereController extends AbstractController
{
    #[Route("/filieres", name: "filieres")]

    public function index(Request $request, EntityManagerInterface $em): Response
    {

        $filiere = new Filiere();
        if ($request->isMethod('POST')) {
            $libelle = $request->request->get('libelle');

            $filiere->setLibelle($libelle);

            $em->persist($filiere);
            $em->flush();

            return $this->redirectToRoute('filiere_list');
        }

        return $this->render('filiere/index.html.twig', [
            'filiere' => $filiere
        ]);
    }

    #[Route("/filieres/list", name: "filiere_list")]

    public function list(EntityManagerInterface $em): Response
    {
        $filieres = $em->getRepository(Filiere::class)->findAll();

        return $this->render('filiere/list.html.twig', [
            'filieres' => $filieres,
        ]);
    }
}
