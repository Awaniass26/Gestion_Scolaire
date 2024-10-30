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

        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom');
            $prenom = $request->request->get('prenom');
            $dateNaissance = $request->request->get('dateNaissance');
            $adresse = $request->request->get('adresse');

            $etudiant->setNom($nom);
            $etudiant->setPrenom($prenom);
            $etudiant->setDateNaissance(new \DateTime('2000-01-01'));
            $etudiant->setAdresse($adresse);

            $errors = $validator->validate($etudiant);

            if (count($errors) > 0) {
                $errorsString = "<ul style='color: red; font-weight: bold;'>";
                foreach ($errors as $error) {
                    $errorsString .= "<li>" . $error->getPropertyPath() . ": " . $error->getMessage() . "</li>";
                }
                $errorsString .= "</ul>";
    
                return new Response($errorsString);
            }

            $em->persist($etudiant);
            $em->flush();

            return $this->redirectToRoute('etudiant_list');
        }


        return $this->render('etudiant/index.html.twig', [
            'etudiant' => $etudiant
        ]);
    }



    #[Route("/etudiants/list", name: "etudiant_list")]

    public function list( Request $request, $em, PaginatorInterface $paginator): Response
    {
        $etudiants = $em->getRepository( Etudiant::class)->findAll();

        $query = $em->getRepository(Etudiant::class)->createQueryBuilder('c')->getQuery();
        $page = $request->query->getInt('page', 1);
        $etudiants = $paginator->paginate($query, $page, 5);


        return $this->render('etudiant/list.html.twig', [
            'etudiants' => $etudiants,
        ]);
    }

    #[Route("/etudiants/edit/{id}", name: "etudiant_edit")]
    public function edit(Request $request, EntityManagerInterface $em,  int $id): Response
    {
        $etudiant = $em->getRepository(Etudiant::class)->find($id);

        if ($request->isMethod('POST')) {
            $em->flush();

            return $this->redirectToRoute('etudiant_list');
        }

        return $this->render('etudiant/edit.html.twig', [
            'etudiant' => $etudiant,
        ]);
    }

    #[Route("/etudiants/delete/{id}", name: "etudiant_delete", methods: ["POST"])]
    public function delete(EntityManagerInterface $em, int $id): Response
    {
        $etudiant = $em->getRepository(Etudiant::class)->find($id);

        if ($etudiant) {
            $em->remove($etudiant);
            $em->flush();
        }

        return $this->redirectToRoute('etudiant_list');
    }
}
