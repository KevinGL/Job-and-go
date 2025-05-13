<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tel_number', TextType::class, [ "label" => "Numéro de tél", "attr" => [ "class" => "form-control mb-3" ], "required" => false ])
            ->add('name', TextType::class, [ "label" => "Nom", "attr" => [ "class" => "form-control mb-3" ], "required" => false ])
            ->add('email', TextType::class, [ "label" => "Adresse email", "attr" => [ "class" => "form-control mb-3" ], "required" => false ])
            ->add('society', TextType::class, [ "label" => "Société", "attr" => [ "class" => "form-control mb-3" ], "required" => false ])
            ->add('contacted', CheckboxType::class, [ "label" => "Contacté ?", "attr" => [ "class" => "m-3" ], "required" => false ])
            ->add("save", SubmitType::class, [ "label" => "Valider", "attr" => [ "class" => "btn btn-primary" ] ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
