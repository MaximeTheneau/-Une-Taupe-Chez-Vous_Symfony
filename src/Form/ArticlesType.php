<?php

namespace App\Form;

use App\Entity\Articles;
use App\Entity\ListArticles;
use App\Entity\Category;
use App\Entity\Subcategory;
use App\Entity\Subtopic;
use Doctrine\ORM\EntityRepository;
use App\Form\SubcategoryType;
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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ArticlesType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('category', EntityType::class, [
            'label' => "Catégorie de l'article",
            'class' => Category::class,
            'choice_label' => 'name',
            'multiple' => false,
            'expanded' => true,
        ]
            )
            ->add('subcategory', EntityType::class, [
                'label' => "Sous-catégorie de l'article",
                'class' => Subcategory::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ]
                )
            ->add('subtopic', EntityType::class, [
                'label' => "Sous-rubriques de l'article",
                'class' => Subtopic::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ]
                )
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true,
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Titre de l\'article* (max 70 caractères)',
                    'maxlength' => '70',
                    ]
            ])
            ->add('contents', TextareaType::class, [
                'label' => 'Paragraphe',
                'required' => true,
                'attr' => [
                    'class' => 'textarea',
                    'placeholder' => 'Paragraphe de l\'article* (max 5000 caractères) ',
                    'maxlength' => '5000',
                ]
            ])
            ->add('imgPost', FileType::class, [
                'label' => false,
                'required' => false,
                'data_class' => null,
                'mapped' => true,
                'attr' => [
                    'class' => 'input',
                    'id' => 'image',
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
            ->add('altImg', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'input mb-3',
                    'placeholder' => 'Texte alternatif de l\'image (max 165 caractères)',
                    'maxlength' => '165',
                ]
            ])
            ->add('listArticles', CollectionType::class, [
                'entry_type' => ListArticlesType::class,
                'required' => false,
                'label' => false,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('links', TextType::class, [
                'label' => 'Lien',
                'required' => false,
                'attr' => [
                    'class' => 'input list-input',
                    'placeholder' => 'ex: https://www.exemple.fr',
                    'maxlength' => '500',
                ]
                ])
            ->add('textLinks', TextType::class, [
                    'label' => 'Texte du lien',
                    'required' => false,
                    'attr' => [
                        'class' => 'input list-input',
                        'placeholder' => 'max 255 caractères',
                        'maxlength' => '255',
                    ]
                    ])
                ->add('paragraphArticles', CollectionType::class, [
                    'entry_type' => ParagraphArticlesType::class,
                    'label' => false,
                    'required' => false,
                    'entry_options' => ['label' => false],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ]);

                $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                    $form = $event->getForm();
                    $listArticles = $event->getData()->getParagraphArticles();
        
                    
                });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
        ]);
    }
}
