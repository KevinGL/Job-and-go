<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        $queryBuilder->orderBy("c.candidacy_date", "DESC");
        
        $candidacies = $queryBuilder->getQuery()->getResult();

        $this->getNeedRelaunch($candidacies);
        
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
                    "La Bonne Boîte" => "La Bonne Boîte",
                    "La Bonne Alternance" => "La Bonne Alternance",
                    "Spontané" => "Spontané",
                    "Autre" => "Autre"
                ],
                "label" => "Type de candidature"
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

            //->add("relaunch_date", DateType::class)

            ->add("comments", TextareaType::class,
            [
                "attr" =>
                [
                    "class" => "form-control"
                ],
                "label" => "Commentaires",
                "required" => false
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

    #[Route('/candidacies/view/{id}', name: 'view_candidacy')]
    public function view($id, CandidacyRepository $repo): Response
    {
        $cand = $repo->find($id);

        $this->getNeedRelaunchOne($cand);

        return $this->render('candidacy/view.html.twig', ["cand" => $cand]);
    }

    #[Route('/candidacies/edit/{id}', name: 'edit_candidacies')]
    public function edit($id, Request $request, CandidacyRepository $repo, EntityManagerInterface $entityManager): Response
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
                        "La Bonne Boîte" => "La Bonne Boîte",
                        "La Bonne Alternance" => "La Bonne Alternance",
                        "Spontané" => "Spontané",
                        "Autre" => "Autre"
                    ],
                    "label" => "Type de candidature"
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
                        "Réponse négative :(" => "no",
                        "Offre expirée :(" => "off",
                    ],
                    "label" => "Issue de la candidature"
                ])
                
				->getForm();
			
			$form->handleRequest($request);	
			
			if($form->isSubmitted() && $form->isValid())
			{
				$entityManager->persist($cand);
                $entityManager->flush();

                return $this->redirectToRoute("app_candidacies");
			}
			
			return $this->render('candidacy/edit.html.twig', ["form" => $form]);
    }

    #[Route('/candidacies/delete/{id}', name: 'delete_candidacies')]
    public function delete($id, CandidacyRepository $repo, EntityManagerInterface $entityManager): Response
    {
        $cand = $repo->find($id);

        $entityManager->remove($cand);
        $entityManager->flush();

        return $this->redirectToRoute("app_candidacies");
    }

    private function getNeedRelaunch(&$candidacies)
    {
        foreach($candidacies as $cand)
        {
            if($cand->getRelaunchDate() == null && $cand->getIssue() == null)
            {
                $now = new \DateTimeImmutable();

                $delay = $now->diff($cand->getCandidacyDate())->format("%a");

                if($delay < 7)
                {
                    $cand->needRelaunch = "#5bfe6a";
                }

                else
                if($delay >= 7 && $delay < 14)
                {
                    $cand->needRelaunch = "yellow";
                }

                else
                if($delay >= 14 && $delay < 21)
                {
                    $cand->needRelaunch = "#f9a33e";
                }

                else
                if($delay >= 21 && $delay < 28)
                {
                    $cand->needRelaunch = "red";
                }

                else
                if($delay >= 28)
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

    private function getNeedRelaunchOne(&$cand)
    {
        if($cand->getRelaunchDate() == null && $cand->getIssue() == null)
        {
            $now = new \DateTimeImmutable();

            $delay = $now->diff($cand->getCandidacyDate())->format("%a");

            if($delay < 7)
            {
                $cand->needRelaunch = "#5bfe6a";
            }

            else
            if($delay >= 7 && $delay < 14)
            {
                $cand->needRelaunch = "yellow";
            }

            else
            if($delay >= 14 && $delay < 21)
            {
                $cand->needRelaunch = "#f9a33e";
            }

            else
            if($delay >= 21 && $delay < 28)
            {
                $cand->needRelaunch = "red";
            }

            else
            if($delay >= 28)
            {
                $cand->needRelaunch = "black";
            }
        }

        else
        {
            $cand->needRelaunch = "white";
        }
    }

    #[Route('/candidacies/deleteserverals/{ids}', name: 'delete_serverals_candidacies')]
    public function deleteSeverals($ids, CandidacyRepository $repo, EntityManagerInterface $entityManager): Response
    {
        $values = explode(",", $ids);

        foreach($values as $value)
        {
            $item = $repo->find($value);

            $entityManager->remove($item);
        }

        $entityManager->flush();

        $response = new JsonResponse();

        $data = ["message" => "Success"];

        $response->setStatusCode(200);
        $response->setData($data);

        return $response;
    }

    #[Route('/candidacies/graph', name: 'graph')]
    public function graph(CandidacyRepository $repo): Response
    {
        $queryBuilder = $repo->createQueryBuilder("c");

        $queryBuilder->orderBy("c.candidacy_date", "ASC");
        
        $candidacies = $queryBuilder->getQuery()->getResult();

        $actualDate = new \DateTimeImmutable();

        $actualYear = $actualDate->format("Y");
        $actualMonth = "10";//$actualDate->format("m");
        $nbDays = cal_days_in_month(CAL_GREGORIAN, $actualMonth, $actualYear);

        $nbCandidaciesPerDay = [];

        for($i=0; $i<$nbDays; $i++)
        {
            $key = "day_" . ($i+1);
            
            $nbCandidaciesPerDay[$key] = 0;
        }

        foreach($candidacies as $c)
        {
            $year = $c->getCandidacyDate()->format("Y");
            $month = $c->getCandidacyDate()->format("m");
            $day = $c->getCandidacyDate()->format("d");

            if($year == $actualYear && $month == $actualMonth)
            {
                $key = "day_" . $day;
                
                $nbCandidaciesPerDay[$key]++;
            }
        }

        return $this->render('candidacy/graph.html.twig',
        [
            "nbCand" => $nbCandidaciesPerDay,
            "nbDays" => $nbDays
        ]);
    }
}
