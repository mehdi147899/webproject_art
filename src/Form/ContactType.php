<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('Nom', TextType::class, [
            'label' => $this->translator->trans('pagecontact.labels.name'),
            'attr' => ['placeholder' => $this->translator->trans('pagecontact.placeholders.name')],
        ])
            ->add('Email', EmailType::class, [
                'label' => $this->translator->trans('pagecontact.labels.email'),
                'attr' => ['placeholder' => $this->translator->trans('pagecontact.placeholders.email')],
            ])
            ->add('Message', TextareaType::class, [
                'label' => $this->translator->trans('pagecontact.labels.message'),
                'attr' => ['placeholder' => $this->translator->trans('pagecontact.placeholders.message')],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
