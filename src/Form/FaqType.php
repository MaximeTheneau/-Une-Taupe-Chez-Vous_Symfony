<?php

namespace App\Form;

use App\Entity\Faq;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class FaqType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('question', TextareaType::class, [
                'label' => 'Question *',
                'required' => true,
                'attr' => [
                    'class' => 'textarea',
                    'placeholder' => 'La question ne doit pas faire plus de 160 caractère.',
                    'maxlength' => '160',
                ]
            ])
            ->add('answer', TextareaType::class, [
                'label' => 'Response *',
                'required' => true,
                'attr' => [
                    'class' => 'textarea',
                    'placeholder' => 'La response ne doit pas faire plus de 500 caractère.',
                    'maxlength' => '500',
                ]
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Faq::class,
        ]);
    }
}
