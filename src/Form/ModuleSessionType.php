<?php

namespace App\Form;

use App\Entity\Programme;
use App\Entity\Module;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ModuleSessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('session')
            ->add('nbJours', IntegerType::class, [
                'label'=>'nombre de jours',
                'required'=>false,
            ])
            ->add('module', EntityType::class, [
                'class'=>Module::class,
                'choice_label'=>'intitulee',
                'placeholder'=> 'Selectionné unn Module',
                'required'=>true,
            ])
            ->add('Valider', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Programme::class,
        ]);
    }
}
