<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Candidacy;
use App\Repository\CandidacyRepository;

class CandidacyController extends AbstractController
{
    #[Route('/candidacies', name: 'app_candidacies')]
    public function index(CandidacyRepository $repo): Response
    {
        $queryBuilder = $repo->createQueryBuilder("c");

        $queryBuilder->orderBy("c.candidacy_date", "ASC");
        
        $candidacies = $queryBuilder->getQuery()->getResult();//$repo->findAll();
        
        return $this->render('candidacy/index.html.twig',
        [
            "candidacies" => $candidacies
        ]);
    }

    #[Route('/candidacies/add', name: 'add_candidacy')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $candidacy = new Candidacy();
        
        $form = $this->createFormBuilder($candidacy)

            ->add("society", TextType::class,
            [
                "attr" =>
                [
                    "class" => "form-control"
                ],
                "label" => "Nom de la société"
            ])

            ->add("type", ChoiceType::class,
            [
                "attr" =>
                [
                    "class" => "form-select"
                ],
                "choices" =>
                [
                    "Pôle Emploi" => "Pôle Emploi",
                    "Indeed" => "Indeed",
                    "LinkedIn" => "LinkedIn",
                    "Spontané" => "Spontané",
                    "Autre" => "Autre"
                ],
                "label" => "Type de candidature"
            ])

            /*->add("candidacy_date", DateType::class,
            [
                "widget" => "single_text",
                "label" => "Date de candidature"
            ])

            ->add("relaunch_date", DateType::class)*/

            ->add("comments", TextareaType::class,
            [
                "attr" =>
                [
                    "class" => "form-control"
                ],
                "label" => "Commentaires"
            ])

            /*->add("issue", TextType::class,
            [
                "attr" =>
                [
                    "class" => "form-control"
                ],
                "label" => "Etat final"
            ])*/

            ->getForm();
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->persist($candidacy);

            $entityManager->flush();

            return $this->redirectToRoute('app_candidacies');
        }
        
        return $this->render('candidacy/add.html.twig', ["form" => $form]);
    }
}
