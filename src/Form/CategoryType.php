<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Subcategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('subcategory', EntityType::class, [
                'label' => "Sous-catégorie de l'article",
                'choice_label' => 'name',
                'class' => Subcategory::class,
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ]
                )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
