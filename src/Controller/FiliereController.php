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

        $filiere = new Filiere();
        if ($request->isMethod('POST')) {
            $libelle = $request->request->get('libelle');

            $filiere->setLibelle($libelle);

            $errors = $validator->validate($filiere);

            if (count($errors) > 0) {
                $errorsString = "<ul style='color: red; font-weight: bold;'>";
                foreach ($errors as $error) {
                    $errorsString .= "<li>" . $error->getPropertyPath() . ": " . $error->getMessage() . "</li>";
                }
                $errorsString .= "</ul>";

                return new Response($errorsString);
            }

            $em->persist($filiere);
            $em->flush();

            return $this->redirectToRoute('filieres');
        } else {
            $filieres = $em->getRepository(Filiere::class)->findAll();

            $query = $em->getRepository(Filiere::class)->createQueryBuilder('c')->getQuery();
            $page = $request->query->getInt('page', 1);
            $filieres = $paginator->paginate($query, $page, 5);
        }
        return $this->render('filiere/index.html.twig', [
            'filiere' => $filiere,
            'filieres' => $filieres
        ]);
    }
}
