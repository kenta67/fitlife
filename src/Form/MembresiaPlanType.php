<?php

namespace App\Form;

use App\Entity\MembresiaPlan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MembresiaPlanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombrePlan', TextType::class, ['label' => 'Nombre del plan'])
            ->add('costo', NumberType::class, [
                'label' => 'Costo (Bs)',
                'scale' => 2,
                'attr' => ['step' => '0.01', 'min' => 0],
            ])
            ->add('duracionDias', IntegerType::class, [
                'label' => 'Duración (días)',
                'attr' => ['min' => 1],
            ])
            ->add('incluyeClases', CheckboxType::class, [
                'label' => 'Incluye acceso a clases',
                'required' => false,
            ])
            ->add('estado', CheckboxType::class, [
                'label' => 'Activo',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MembresiaPlan::class,
        ]);
    }
}
