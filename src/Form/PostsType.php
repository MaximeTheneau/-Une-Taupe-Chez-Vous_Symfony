<?php

namespace App\Form;

use App\Entity\Posts;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class PostsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du Service *',
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Titre du Service',
                    'maxlength' => '70',
                ]
            ])
            ->add('contents', TextareaType::class, [
                'label' => 'Paragraphe du Service *',
                'required' => false,
                'attr' => [
                    'class' => 'textarea',
                    'placeholder' => 'Paragraphe du Service',
                    'maxlength' => '750',
                ]
            ])
            ->add('subtitle', TextType::class, [
                'label' => 'Sous-titre du Service ',
                'required' => false,
                'attr' => [
                    'maxlength' => '160',
                    'class' => 'input',
                    'placeholder' => 'Sous-titre du Service',
                ]
            ])
            ->add('subtitle2', TextType::class, [
                'label' => '2éme sous-titre du Service ',
                'required' => false,
                'attr' => [
                    'class' => 'input',
                    'placeholder' => '2éme sous-titre du Service',
                    'maxlength' => '160',
                ]
            ])
            ->add('contents2', TextareaType::class, [
                'label' => '2éme Paragraphe du Service',
                'required' => false,
                'attr' => [
                    'class' => 'textarea',
                    'placeholder' => '2éme Paragraphe du Service',
                    'maxlength' => '750',
                ]
            ])
            ->add('contents3', TextareaType::class, [
                'label' => '3éme Paragraphe du Service',
                'required' => false,
                'attr' => [
                    'class' => 'textarea',
                    'placeholder' => '3éme Paragraphe du Service',
                    'maxlength' => '750',
                ]
            ])
            ->add('imgPost', FileType::class, [
                'label' => 'Image de couverture *',
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
            ],)
            ->add('imgPost2', FileType::class, [
                'label' => '2ème image du Services',
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
            ],)
            ->add('imgPost3', FileType::class, [
                'attr' => [
                    'class' => 'input',
                ],
                'label' => '3ème image du Services',
                'required' => false,
                'data_class' => null,
                'mapped' => false,
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
            ->add('imgPost4', FileType::class, [
                'label' => '4ème image du Services',
                'attr' => [
                    'class' => 'input',
                ],
                'required' => false,
                'data_class' => null,
                'mapped' => false,
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Posts::class,
        ]);
    }
}
