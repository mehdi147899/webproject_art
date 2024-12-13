<?php

namespace App\Form;

use App\Entity\Evenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nameFr', TextType::class, [
                'label' => 'Nom (Français)',
                'required' => false,
            ])
            ->add('nameEn', TextType::class, [
                'label' => 'Name (English)',
                'required' => false,
            ])
            ->add('nameDe', TextType::class, [
                'label' => 'Name (Deutsch)',
                'required' => false,
            ])
            ->add('descriptionFr', TextareaType::class, [
                'label' => 'Description (Français)',
                'required' => false,
            ])
            ->add('descriptionEn', TextareaType::class, [
                'label' => 'Description (English)',
                'required' => false,
            ])
            ->add('descriptionDe', TextareaType::class, [
                'label' => 'Description (Deutsch)',
                'required' => false,
            ])
            ->add('date', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Lieu',
            ])
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}
