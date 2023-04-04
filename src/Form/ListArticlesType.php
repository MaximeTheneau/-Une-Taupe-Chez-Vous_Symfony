<?php

namespace App\Form;

use App\Entity\ListArticles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



class ListArticlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la liste',
                'required' => false,
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'max 170 caractères',
                    'maxlength' => '170',
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de la liste',
                'required' => false,
                'attr' => [
                    'class' => 'textarea',
                    'placeholder' => 'max 5000 caractères',
                    'maxlength' => '5000',
                ]
            ])
            
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ListArticles::class,
        ]);
    }
}
