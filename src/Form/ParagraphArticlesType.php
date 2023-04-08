<?php

namespace App\Form;

use App\Entity\ParagraphArticles;
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



class ParagraphArticlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('imgPostParagh', FileType::class, [
                'label' => 'Image du paragraphe',
                'required' => false,
                'data_class' => null,
                'mapped' => true,
                'attr' => [
                    'placeholder' => 'max 5Mo',
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
            ],)
        ->add('subtitle', TextType::class, [
            'label' => 'Sous-titre',
            'required' => true,
            'attr' => [
                'class' => 'input',
                'placeholder' => 'Sous-titre du paragraphe (max 170 caractères)',
                'maxlength' => '170',
                ]
                ])
            ->add('paragraph', TextareaType::class, [
                'label' => 'Paragraphe',
                'attr' => [
                    'class' => 'textarea',
                    'placeholder' => 'Paragraphe de l\'article (max 5000 caractères)',
                    'maxlength' => '5000',
                    ]
            ])

                
                
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ParagraphArticles::class,
        ]);
    }
}
