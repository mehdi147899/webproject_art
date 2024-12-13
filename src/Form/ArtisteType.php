<?php

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
                'attr' => ['placeholder' => 'Enter the artist\'s last name'],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'First Name',
                'attr' => ['placeholder' => 'Enter the artist\'s first name'],
            ])
            ->add('bioFr', TextareaType::class, [
                'label' => 'Biography (French)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Enter the artist\'s biography in French',
                    'rows' => 5,
                ],
            ])
            ->add('bioEn', TextareaType::class, [
                'label' => 'Biography (English)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Enter the artist\'s biography in English',
                    'rows' => 5,
                ],
            ])
            ->add('bioDe', TextareaType::class, [
                'label' => 'Biography (German)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Enter the artist\'s biography in German',
                    'rows' => 5,
                ],
            ])
            ->add('date_de_naissance', DateType::class, [
                'label' => 'Date of Birth',
                'widget' => 'single_text',
                'attr' => ['placeholder' => 'Select the artist\'s birth date'],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'mapped' => false, // Not directly mapped to the entity
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Please upload a valid image (JPEG/PNG)',
                    ]),
                ],
            ])
            ->add('videoUrls', TextareaType::class, [
                'label' => 'Video URLs',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Enter video URLs separated by commas',
                    'rows' => 3,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Artiste::class,
        ]);
    }
}
