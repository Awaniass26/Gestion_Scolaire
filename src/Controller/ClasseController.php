<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Entity\Etudiant;
use App\Entity\Filiere;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClasseController extends AbstractController
{
    #[Route("/classes", name: "classes")]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $classe = new Classe();

        if ($request->isMethod('POST')) {
            $libelle = $request->request->get('libelle');
            $niveau = $request->request->get('niveau');
            $filiereId = $request->request->get('filiereId');
            $etudiantId = $request->request->get('etudiantId');

            $classe->setLibelle($libelle);
            $classe->setNiveau($niveau);

            if ($filiereId) {
                $filiere = $em->getRepository(Filiere::class)->find($filiereId);
                $classe->setFiliereId($filiere);
            }

            if ($etudiantId) {
                $etudiant = $em->getRepository(Etudiant::class)->find($etudiantId);
                $classe->setEtudiantId($etudiant);
            }

            $em->persist($classe);
            $em->flush();

            return $this->redirectToRoute('classe_list');
        }

        $filieres = $em->getRepository(Filiere::class)->findAll();
        $etudiants = $em->getRepository(Etudiant::class)->findAll();

        return $this->render('classe/index.html.twig', [
            'classe' => $classe,
            'filieres' => $filieres,
            'etudiants' => $etudiants,
        ]);
    }

    #[Route("/classes/list", name: "classe_list")]
    public function list(EntityManagerInterface $em): Response
    {
        $classes = $em->getRepository(Classe::class)->findAll();

        return $this->render('classe/list.html.twig', [
            'classes' => $classes,
        ]);
    }
}
