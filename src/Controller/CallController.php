<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CallRepository;
use App\Entity\Call;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CallController extends AbstractController
{
    #[Route('/calls', name: 'app_calls')]
    public function index(CallRepository $repo): Response
    {
        $queryBuilder = $repo->createQueryBuilder("c");

        $queryBuilder->orderBy("c.date", "DESC");
        
        $calls = $queryBuilder->getQuery()->getResult();
        
        return $this->render('call/index.html.twig',
        [
            'calls' => $calls,
        ]);
    }

    #[Route('/calls/add', name: 'add_call')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $call = new Call();
        
        $form = $this->createFormBuilder($call)

        ->add("society", TextType::class,
        [
            "attr" =>
            [
                "class" => "form-control"
            ],
            "label" => "Nom de la société"
        ])

        ->add("telNumber", TextType::class,
        [
            "attr" =>
            [
                "class" => "form-control"
            ],
            "label" => "Numéro de téléphone"
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

        ->add("date", DateType::class,
        [
            "widget" => "single_text",
            "label" => "Date de l'appel",
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
            "label" => "Poste recherché"
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
            $entityManager->persist($call);

            $entityManager->flush();

            return $this->redirectToRoute('app_calls');
        }
        
        return $this->render('call/add.html.twig', ["form" => $form]);
    }

    #[Route('/calls/view/{id}', name: 'view_call')]
    public function view($id, CallRepository $repo): Response
    {
        $call = $repo->find($id);

        return $this->render('call/view.html.twig', ["call" => $call]);
    }

    #[Route('/calls/edit/{id}', name: 'edit_call')]
    public function edit($id, Request $request, CallRepository $repo, EntityManagerInterface $entityManager): Response
    {
        $call = $repo->find($id);

        $form = $this->createFormBuilder($call)

        ->add("society", TextType::class,
        [
            "attr" =>
            [
                "class" => "form-control"
            ],
            "label" => "Nom de la société"
        ])

        ->add("telNumber", TextType::class,
        [
            "attr" =>
            [
                "class" => "form-control"
            ],
            "label" => "Numéro de téléphone"
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

        ->add("date", DateType::class,
        [
            "widget" => "single_text",
            "label" => "Date de l'appel",
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
            "label" => "Poste recherché"
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
            $entityManager->persist($call);
            $entityManager->flush();

            return $this->redirectToRoute("app_calls");
        }
        
        return $this->render('call/edit.html.twig', ["form" => $form]);
    }

    #[Route('/calls/delete/{id}', name: 'delete_call')]
    public function delete($id, CallRepository $repo, EntityManagerInterface $entityManager): Response
    {
        $call = $repo->find($id);

        $entityManager->remove($call);
        $entityManager->flush();

        return $this->redirectToRoute("app_calls");
    }
}
