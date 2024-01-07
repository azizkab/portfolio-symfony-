<?php
// src/Form/SkillType.php

namespace App\Form;

use App\Entity\Skill;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class SkillType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        

            ->add('name', TextType::class, [
                'label' => 'Nom de la compétence',
                'required' => true,
            ])
            ->add('project', TextType::class, [
                'label' => 'Projet associé',
                'required' => true,
            ])
          
            ->add('description', TextareaType::class, [
                'label' => 'Description de la compétence',
            ])
            ;;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Skill::class,
        ]);
    }
}
