<?php

namespace App\Controller;

use App\Entity\Filiere;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Knp\Component\Pager\PaginatorInterface;

class FiliereController extends AbstractController
{
    #[Route("/filieres", name: "filieres")]
    public function index(Request $request, EntityManagerInterface $em, ValidatorInterface $validator, PaginatorInterface $paginator): Response
    {
        $errors = [];
        $filiere = new Filiere();
        
        // Handle form submission
        if ($request->isMethod('POST')) {
            $libelle = $request->request->get('libelle');
            $filiere->setLibelle($libelle);

            $violations = $validator->validate($filiere);
            if (count($violations) > 0) {
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
            } else {
                $em->persist($filiere);
                $em->flush();
                return $this->redirectToRoute('filieres');
            }
        }

        // Retrieve all 'Filieres' with pagination
        $query = $em->getRepository(Filiere::class)->createQueryBuilder('f')->getQuery();
        $page = $request->query->getInt('page', 1);
        $filieres = $paginator->paginate($query, $page, 5);

        return $this->render('filiere/index.html.twig', [
            'filiere' => $filiere,
            'filieres' => $filieres,
            'errors' => $errors,
        ]);
    }

    #[Route("/filieres/edit/{id}", name: "filiere_edit")]
    public function edit(int $id, Request $request, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $errors = [];
        $filiere = $em->getRepository(Filiere::class)->find($id);

        if (!$filiere) {
            throw $this->createNotFoundException('Filière non trouvée.');
        }

        // Handle form submission for editing
        if ($request->isMethod('POST')) {
            $libelle = $request->request->get('libelle');
            $filiere->setLibelle($libelle);

            $violations = $validator->validate($filiere);
            if (count($violations) > 0) {
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
            } else {
                $em->flush();
                return $this->redirectToRoute('filieres');
            }
        }

        return $this->render('filiere/index.html.twig', [
            'filiere' => $filiere,
            'filieres' => [],
            'errors' => $errors,
        ]);
    }
}
