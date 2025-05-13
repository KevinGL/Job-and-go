<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Contact;
use App\Form\ContactFormType;

class ContactController extends AbstractController
{
    #[Route('/contacts', name: 'app_contacts')]
    public function index(ContactRepository $repo): Response
    {
        $contacts = $repo->findAll();
        
        return $this->render('contact/index.html.twig', [
            'contacts' => $contacts,
        ]);
    }

    #[Route('/contact/add', name: 'add_contact')]
    public function add(Request $req, EntityManagerInterface $em): Response
    {
        $contact = new Contact();

        $form = $this->createForm(ContactFormType::class, $contact);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid())
        {
            $em->persist($contact);
            $em->flush();

            $this->addFlash("success", "Contact ajouté");

            return $this->redirectToRoute("app_contacts");
        }

        return $this->render('contact/add.html.twig', [
            'myForm' => $form,
        ]);
    }

    #[Route("/contact/view/{id}", name: "view_contact")]
    public function view(int $id, ContactRepository $repo): Response
    {
        $contact = $repo->find($id);

        return $this->render("contact/view.html.twig", ["contact" => $contact]);
    }

    #[Route('/contact/edit/{id}', name: 'edit_contact')]
    public function edit(int $id, Request $req, ContactRepository $repo, EntityManagerInterface $em): Response
    {
        $contact = $repo->find($id);

        $form = $this->createForm(ContactFormType::class, $contact);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid())
        {
            $em->flush();

            $this->addFlash("success", "Contact mis à jour");

            return $this->redirectToRoute("app_contacts");
        }

        return $this->render('contact/edit.html.twig', [
            'myForm' => $form,
        ]);
    }

    #[Route('/contact/delete/{id}', name: 'delete_contact')]
    public function delete(int $id, ContactRepository $repo, EntityManagerInterface $em): Response
    {
        $contact = $repo->find($id);

        $em->remove($contact);
        $em->flush();

        $this->addFlash("success", "Le contact a bien été supprimé");
        return $this->redirectToRoute("app_contacts");
    }
}
