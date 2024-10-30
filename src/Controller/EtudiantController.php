<?php

namespace App\Controller;

use App\Entity\Etudiant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Knp\Component\Pager\PaginatorInterface;

class EtudiantController extends AbstractController
{
    #[Route("/etudiants", name: "etudiants")]
    public function index(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $etudiant = new Etudiant();
        $errors = [];

        if ($request->isMethod('POST')) {
            $errors = $this->handleEtudiantForm($request, $etudiant, $validator, $em);
            if (empty($errors)) {
                return $this->redirectToRoute('etudiant_list');
            }
        }

        return $this->render('etudiant/index.html.twig', [
            'etudiant' => $etudiant,
            'error' => $errors
        ]);
    }

    #[Route("/etudiants/list", name: "etudiant_list")]
    public function list(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        $query = $em->getRepository(Etudiant::class)->createQueryBuilder('e')->getQuery();
        $page = $request->query->getInt('page', 1);
        $etudiants = $paginator->paginate($query, $page, 5);

        return $this->render('etudiant/list.html.twig', [
            'etudiants' => $etudiants,
        ]);
    }

    #[Route("/etudiants/edit/{id}", name: "etudiant_edit")]
    public function edit(Request $request, EntityManagerInterface $em, ValidatorInterface $validator, int $id): Response
    {
        $etudiant = $em->getRepository(Etudiant::class)->find($id);
        if (!$etudiant) {
            throw $this->createNotFoundException('Etudiant non trouvée.');
        }

        $errors = [];
        if ($request->isMethod('POST')) {
            $errors = $this->handleEtudiantForm($request, $etudiant, $validator, $em);
            if (empty($errors)) {
                return $this->redirectToRoute('etudiant_list');
            }
        }

        return $this->render('etudiant/index.html.twig', [
            'etudiant' => $etudiant,
            'error' => $errors
        ]);
    }

    private function handleEtudiantForm(Request $request, Etudiant $etudiant, ValidatorInterface $validator, EntityManagerInterface $em): array
    {
        $nom = $request->request->get('nom');
        $prenom = $request->request->get('prenom');
        $adresse = $request->request->get('adresse');

        // Set name and address
        $etudiant->setNom($nom);
        $etudiant->setPrenom($prenom);
        $etudiant->setAdresse($adresse);

        // Handle date of birth
        $dateNaissanceString = $request->request->get('dateNaissance');
        if ($dateNaissanceString) {
            try {
                $dateNaissance = new \DateTime($dateNaissanceString);
                $etudiant->setDateNaissance($dateNaissance);
            } catch (\Exception $e) {
                return ['dateNaissance' => 'Date format is invalid.'];
            }
        }

        // Validate
        $violations = $validator->validate($etudiant);
        $errors = [];
        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }
        } else {
            $em->persist($etudiant);
            $em->flush();
        }

        return $errors;
    }
}
