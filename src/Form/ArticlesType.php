<?php

namespace App\Form;

use App\Entity\Articles;
use App\Entity\ListArticles;
use App\Entity\Category;
use App\Entity\Subcategory;
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
        // ->add('category', EntityType::class, [
        //     'label' => "Catégorie de l'article",
        //     'class' => Category::class,
        //     'choice_label' => 'name',
        //     'multiple' => true,
        //     'expanded' => true,
        // ]
        //     )
            ->add('category', TextType::class, [
                'label' => "Nouvelle catégorie",
                'required' => false,
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'article*',
                'required' => true,
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'max 70 caractères',
                    'maxlength' => '70',
                    ]
            ])
            ->add('contents', TextareaType::class, [
                'label' => 'Paragraphe de l\'article*',
                'required' => true,
                'attr' => [
                    'class' => 'textarea',
                    'placeholder' => 'max 5000 caractères ',
                    'maxlength' => '5000',
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
                    'entry_options' => ['label' => 'et'],
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
