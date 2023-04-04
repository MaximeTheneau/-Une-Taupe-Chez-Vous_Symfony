<?php

namespace App\Form;

use App\Entity\Pages;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;



class PagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la page *',
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Le titre de l\'article',
                    'maxlength' => '70',
                    ]
            ])
            ->add('subtitle', TextareaType::class, [
                'label' => 'Sous-titre de la page *',
                'required' => false,
                'attr' => [
                    'class' => 'textarea',
                    'placeholder' => 'Sous-titre de la page',
                ]
            ])
            ->add('contents', TextareaType::class, [
                'label' => '1er paragraphe de la page *',
                'required' => false,
                'attr' => [
                    'class' => 'textarea',
                    'placeholder' => 'Paragraphe 1',
                    'maxlength' => '750',
                ]
            ])
            ->add('contents2', TextareaType::class, [
                'label' => '2ème paragraphe de la page * ',
                'required' => false,
                'attr' => [
                    'class' => 'textarea',
                    'placeholder' => 'Paragraphe 2',
                    'maxlength' => '750',
                ]
            ])
            ->add('imgHeader',
                FileType::class,
                [
                    'label' => '1er image de la page *',
                    'mapped' => false,
                    'required' => false,
                    'data_class' => null,
                    'mapped' => false,
                    'attr' => [
                        'class' => 'input',
                    ],
                    'constraints' => [
                        new File([
                            'maxSize' => '5M',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/webp',
                                'image/png',
                            ],
                            'mimeTypesMessage' => 'Veuillez uploader une image valide', 
                        ])
                    ],
                ],
            )

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pages::class,
        ]);
    }
}
