<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du projet',
                'attr' => [
                    'placeholder' => 'Entrez le nom du projet'
                ]
            ])
            ->add('description', TextType::class, [
                'label' => 'Description du projet',
                'attr' => [
                    'placeholder' => 'Entrez la description du projet'
                ]
            ])
            ->add('date', DateType::class, [
                'label' => 'Date de debut du projet',
                "data" => new \DateTime(),
                'attr' => [
                    'placeholder' => 'Entrez la date de debut du projet'
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Image du projet',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                            'image/gif',
                            'image/webp',
                            'image/svg+xml'
                        ],
                        'mimeTypesMessage' => 'Veuillez entrer une image valide',
                    ])
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Créer le projet',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class
        ]);
    }
}
