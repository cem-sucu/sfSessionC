<?php

namespace App\Form;

use App\Entity\Session;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class SessionAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nbrePlace', IntegerType::class, [
                'label'=> 'Nombre de place',
                'required'=> 'true',
            ])
            ->add('dateDebut', DateType::class,[
                'label' => 'Date de début',
                'widget'=> 'single_text',
            ])
            ->add('dateFin', DateType::class,[
                'label' => 'Date de fin',
                'widget'=> 'single_text',
            ])
            ->add('nom', TextType::class)
            ->add('formation')
            // ->add('stagiaires')
            ->add('Valider', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}
