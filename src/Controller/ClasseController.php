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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Knp\Component\Pager\PaginatorInterface;

class ClasseController extends AbstractController
{
    #[Route("/classes", name: "classes")]
    public function index(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): Response
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

            $errors = $validator->validate($classe);

            if (count($errors) > 0) {
                $errorsString = "<ul style='color: red; font-weight: bold;'>";
                foreach ($errors as $error) {
                    $errorsString .= "<li>" . $error->getPropertyPath() . ": " . $error->getMessage() . "</li>";
                }
                $errorsString .= "</ul>";

                return new Response($errorsString);
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
    public function list(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        $query = $em->getRepository(Classe::class)->createQueryBuilder('c')->getQuery();
        $page = $request->query->getInt('page', 1);
        $classes = $paginator->paginate($query, $page, 5);

        return $this->render('classe/list.html.twig', [
            'classes' => $classes,
        ]);
    }

    #[Route("/classes/edit/{id}", name: "classe_edit")]
    public function edit(Request $request, EntityManagerInterface $em, ValidatorInterface $validator, int $id): Response
    {
        $classe = $em->getRepository(Classe::class)->find($id);

        if (!$classe) {
            throw $this->createNotFoundException('Classe non trouvée.');
        }

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

            $errors = $validator->validate($classe);
            if (count($errors) > 0) {
                $errorsString = "<ul style='color: red; font-weight: bold;'>";
                foreach ($errors as $error) {
                    $errorsString .= "<li>" . $error->getPropertyPath() . ": " . $error->getMessage() . "</li>";
                }
                $errorsString .= "</ul>";

                return new Response($errorsString);
            }

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

    #[Route("/classes/delete/{id}", name: "classe_delete")]
    public function delete(EntityManagerInterface $em, int $id): Response
    {
        $classe = $em->getRepository(Classe::class)->find($id);

        if ($classe) {
            $em->remove($classe);
            $em->flush();
        }

        return $this->redirectToRoute('classe_list');
    }
}
