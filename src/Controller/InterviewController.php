<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InterviewRepository;
use App\Entity\Interview;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class InterviewController extends AbstractController
{
    #[Route('/interview', name: 'app_interview')]
    public function index(InterviewRepository $repo): Response
    {
        $queryBuilder = $repo->createQueryBuilder("i");

        $queryBuilder->orderBy("i.date", "DESC");
        
        $interviews = $queryBuilder->getQuery()->getResult();

        $this->getNeedRelaunch($interviews);
        
        return $this->render('interview/index.html.twig',
        [
            'interviews' => $interviews,
        ]);
    }

    #[Route('/interview/add', name: 'add_interview')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $params = $request->query->all();
        
        $interview = new interview();

        if(isset($params["society"]) && isset($params["type"]) && isset($params["job"]) && isset($params["comments"]))
        {
            $interview->setSociety($params["society"]);
            $interview->setType($params["type"]);
            $interview->setJob($params["job"]);
            $interview->setComments($params["comments"]);
        }
        
        $form = $this->createFormBuilder($interview)

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
                "France Travail" => "France Travail",
                "Indeed" => "Indeed",
                "Hello Work" => "Hello Work",
                "Welcome to the jungle" => "Welcome to the jungle",
                "Meteo Job" => "Meteo Job",
                "LinkedIn" => "LinkedIn",
                "La Bonne Boîte" => "La Bonne Boîte",
                "La Bonne Alternance" => "La Bonne Alternance",
                "Spontané" => "Spontané",
                "Interim" => "Interim",
                "Autre" => "Autre"
            ],
            "label" => "Type de candidature"
        ])

        ->add("searchedContract", ChoiceType::class,
        [
            "attr" =>
            [
                "class" => "form-select"
            ],
            "choices" =>
            [
                "CDD/CDI" => "CDD/CDI",
                "Stage" => "Stage",
                "Alternance" => "Alternance",
                "Autre" => "Autre"
            ],
            "label" => "Type de contrat recherché"
        ])

        ->add("date", DateType::class,
        [
            "widget" => "single_text",
            "label" => "Date de l'entretien",
            "attr" =>
            [
                "class" => "form-control form-icon-trailing"
            ],
            "data" => new \DateTimeImmutable()
        ])

        ->add("job", TextType::class,
        [
            "attr" =>
            [
                "class" => "form-control"
            ],
            "label" => "Poste proposé / recherché"
        ])

        ->add("comments", TextareaType::class,
        [
            "attr" =>
            [
                "class" => "form-control"
            ],
            "label" => "Commentaires",
            "required" => false
        ])

        ->getForm();
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->persist($interview);

            $entityManager->flush();

            return $this->redirectToRoute('app_interview');
        }
        
        return $this->render('interview/add.html.twig', ["form" => $form]);
    }

    #[Route('/interview/view/{id}', name: 'view_interview')]
    public function view($id, InterviewRepository $repo): Response
    {
        $int = $repo->find($id);

        $this->getNeedRelaunchOne($int);

        return $this->render('interview/view.html.twig', ["int" => $int]);
    }

    #[Route('/interview/edit/{id}', name: 'edit_interview')]
    public function edit($id, Request $request, InterviewRepository $repo, EntityManagerInterface $entityManager): Response
    {
        $int = $repo->find($id);

        $form = $this->createFormBuilder($int)

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
                "France Travail" => "France Travail",
                "Indeed" => "Indeed",
                "Hello Work" => "Hello Work",
                "Welcome to the jungle" => "Welcome to the jungle",
                "Meteo Job" => "Meteo Job",
                "LinkedIn" => "LinkedIn",
                "La Bonne Boîte" => "La Bonne Boîte",
                "La Bonne Alternance" => "La Bonne Alternance",
                "Spontané" => "Spontané",
                "Interim" => "Interim",
                "Autre" => "Autre"
            ],
            "label" => "Type de candidature"
        ])

        ->add("searchedContract", ChoiceType::class,
        [
            "attr" =>
            [
                "class" => "form-select"
            ],
            "choices" =>
            [
                "CDD/CDI" => "CDD/CDI",
                "Stage" => "Stage",
                "Alternance" => "Alternance",
                "Autre" => "Autre"
            ],
            "label" => "Type de contrat recherché"
        ])

        ->add("date", DateType::class,
        [
            "widget" => "single_text",
            "label" => "Date de candidature",
            "attr" =>
            [
                "class" => "form-control form-icon-trailing"
            ]
        ])

        ->add("relaunchDate", DateType::class,
        [
            "widget" => "single_text",
            "label" => "Date de relance",
            "attr" =>
            [
                "class" => "form-control form-icon-trailing"
            ],
            "required" => false
        ])

        ->add("comments", TextareaType::class,
        [
            "attr" =>
            [
                "class" => "form-control"
            ],
            "label" => "Commentaires",
            "required" => false
        ])

        ->add("issue", ChoiceType::class,
        [
            "attr" =>
            [
                "class" => "form-select"
            ],
            "choices" =>
            [
                "Pas de réponse pour le moment" => null,
                "Poste décroché :)" => "ok",
                "Réponse négative :(" => "no"
            ],
            "label" => "Issue de la candidature"
        ])
        
        ->getForm();
        
        $form->handleRequest($request);	
        
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->persist($int);
            $entityManager->flush();

            return $this->redirectToRoute("app_interview");
        }
        
        return $this->render('interview/edit.html.twig', ["form" => $form]);
    }

    #[Route('/interview/delete/{id}', name: 'delete_interview')]
    public function delete($id, InterviewRepository $repo, EntityManagerInterface $entityManager): Response
    {
        $int = $repo->find($id);

        $entityManager->remove($int);
        $entityManager->flush();

        return $this->redirectToRoute("app_interview");
    }

    private function getNeedRelaunch(&$interview)
    {
        foreach($interview as $int)
        {
            if($int->getRelaunchDate() == null && $int->getIssue() == null)
            {
                $now = new \DateTimeImmutable();

                $delay = $now->diff($int->getDate())->format("%a");

                if($delay < 7)
                {
                    $int->needRelaunch = "#5bfe6a";
                }

                else
                if($delay >= 7 && $delay < 14)
                {
                    $int->needRelaunch = "yellow";
                }

                else
                if($delay >= 14 && $delay < 21)
                {
                    $int->needRelaunch = "#f9a33e";
                }

                else
                if($delay >= 21 && $delay < 28)
                {
                    $int->needRelaunch = "red";
                }

                else
                if($delay >= 28)
                {
                    $int->needRelaunch = "black";
                }
            }

            else
            {
                $int->needRelaunch = "white";
            }
        }
    }

    private function getNeedRelaunchOne(&$int)
    {
        if($int->getRelaunchDate() == null && $int->getIssue() == null)
        {
            $now = new \DateTimeImmutable();

            $delay = $now->diff($int->getDate())->format("%a");

            if($delay < 7)
            {
                $int->needRelaunch = "#5bfe6a";
            }

            else
            if($delay >= 7 && $delay < 14)
            {
                $int->needRelaunch = "yellow";
            }

            else
            if($delay >= 14 && $delay < 21)
            {
                $int->needRelaunch = "#f9a33e";
            }

            else
            if($delay >= 21 && $delay < 28)
            {
                $int->needRelaunch = "red";
            }

            else
            if($delay >= 28)
            {
                $int->needRelaunch = "black";
            }
        }

        else
        {
            $int->needRelaunch = "white";
        }
    }
}
