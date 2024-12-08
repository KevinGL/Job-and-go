<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PartialCandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\PartialCand;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PartialCandController extends AbstractController
{
    #[Route('/partial_cand', name: 'app_partial_cand')]
    public function index(PartialCandRepository $repo): Response
    {
        $queryBuilder = $repo->createQueryBuilder("c");

        $queryBuilder->orderBy("c.candidacy_date", "DESC");
        
        $candidacies = $queryBuilder->getQuery()->getResult();
        
        return $this->render('partial_cand/index.html.twig',
        [
            'candidacies' => $candidacies,
        ]);
    }

    #[Route('/partial_cand/add', name: 'add_part_cand')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $candidacy = new PartialCand();
        
        $form = $this->createFormBuilder($candidacy)

        ->add("society", TextType::class,
        [
            "attr" =>
            [
                "class" => "form-control"
            ],
            "label" => "Nom de la société"
        ])

        ->add("contractSearched", ChoiceType::class,
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

        ->add("candidacy_date", DateType::class,
        [
            "widget" => "single_text",
            "label" => "Date de candidature",
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
            $entityManager->persist($candidacy);

            $entityManager->flush();

            return $this->redirectToRoute('app_partial_cand');
        }
        
        return $this->render('partial_cand/add.html.twig', ["form" => $form]);
    }

    #[Route('/partial_cand/view/{id}', name: 'view_partial_cand')]
    public function view($id, PartialCandRepository $repo): Response
    {
        $cand = $repo->find($id);

        $this->getNeedRelaunchOne($cand);

        return $this->render('partial_cand/view.html.twig', ["cand" => $cand]);
    }

    #[Route('/partial_cand/edit/{id}', name: 'edit_partial_cand')]
    public function edit($id, Request $request, PartialCandRepository $repo, EntityManagerInterface $entityManager): Response
    {
        $cand = $repo->find($id);

        $form = $this->createFormBuilder($cand)

            ->add("society", TextType::class,
            [
                "attr" =>
                [
                    "class" => "form-control"
                ],
                "label" => "Nom de la société"
            ])

            ->add("contractSearched", ChoiceType::class,
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

            ->add("candidacy_date", DateType::class,
            [
                "widget" => "single_text",
                "label" => "Date de candidature",
                "attr" =>
                [
                    "class" => "form-control form-icon-trailing"
                ]
            ])

            ->add("relaunch_date", DateType::class,
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
                    "Réponse positive :)" => "ok",
                    "Réponse négative :(" => "no"
                ],
                "label" => "Issue de la candidature"
            ])
            
            ->getForm();
			
			$form->handleRequest($request);	
			
			if($form->isSubmitted() && $form->isValid())
			{
				$entityManager->persist($cand);
                $entityManager->flush();

                return $this->redirectToRoute("app_partial_cand");
			}
			
		return $this->render('partial_cand/edit.html.twig', ["form" => $form]);
    }

    #[Route('/partial_cand/delete/{id}', name: 'delete_partial_cand')]
    public function delete($id, PartialCandRepository $repo, EntityManagerInterface $entityManager): Response
    {
        $cand = $repo->find($id);

        $entityManager->remove($cand);
        $entityManager->flush();

        return $this->redirectToRoute("app_partial_cand");
    }

    private function getNeedRelaunchOne(&$cand)
    {
        if($cand->getRelaunchDate() == null && $cand->getIssue() == null)
        {
            $now = new \DateTimeImmutable();

            $delay = $now->diff($cand->getCandidacyDate())->format("%a");

            if($delay < 28)
            {
                $cand->needRelaunch = "#5bfe6a";
            }

            else
            if($delay >= 28 && $delay < 35)
            {
                $cand->needRelaunch = "yellow";
            }

            else
            if($delay >= 35 && $delay < 42)
            {
                $cand->needRelaunch = "#f9a33e";
            }

            else
            if($delay >= 42 && $delay < 49)
            {
                $cand->needRelaunch = "red";
            }

            else
            if($delay >= 49)
            {
                $cand->needRelaunch = "black";
            }
        }

        else
        {
            $cand->needRelaunch = "white";
        }
    }
}
