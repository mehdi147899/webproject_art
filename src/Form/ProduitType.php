<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Nom', TextType::class, [
                'attr' => ['placeholder' => 'Nom du produit', 'class' => 'form-control'],
                'label' => 'Nom'
            ])
            ->add('Description', TextType::class, [
                'attr' => ['placeholder' => 'Description', 'class' => 'form-control'],
                'label' => 'Description'
            ])
            ->add('Prix', NumberType::class, [
                'attr' => ['placeholder' => 'Prix', 'class' => 'form-control'],
                'label' => 'Prix'
            ])
            ->add('Categorie', ChoiceType::class, [
                'choices' => [
                    'Galerie' => 'Galerie',
                    'Apern Galerie' => 'Apern Galerie'
                ],
                'attr' => ['class' => 'form-control'],
                'label' => 'Catégorie'
            ])
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'label' => 'Image'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
