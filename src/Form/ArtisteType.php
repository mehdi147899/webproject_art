<?php

// src/Form/ArtisteType.php

namespace App\Form;

use App\Entity\Artiste;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class ArtisteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Nom', TextType::class, [
                'label' => 'Name',
            ])
            ->add('prenom', TextType::class, [
                'label' => 'First Name',
            ])
            ->add('bio', TextareaType::class, [
                'label' => 'Biography',
            ])
            ->add('date_de_naissance', DateType::class, [
                'label' => 'Date of Birth',
                'widget' => 'single_text',
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'mapped' => false, // We do not want to map this to the entity directly
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Please upload a valid image (JPEG/PNG)',
                    ]),
                ],
            ])
            ->add('videoUrls', TextareaType::class, [
                'required' => false,
                'mapped' => false, // Keep it unmapped as we are manually handling it
                'attr' => ['placeholder' => 'Enter video URLs separated by commas'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Artiste::class,
        ]);
    }
}
