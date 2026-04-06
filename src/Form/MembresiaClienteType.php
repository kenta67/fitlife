<?php

namespace App\Form;

use App\Entity\MembresiaCliente;
use App\Entity\Cliente;
use App\Entity\MembresiaPlan;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MembresiaClienteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cliente', EntityType::class, [
                'class' => Cliente::class,
                'choice_label' => function (Cliente $cliente) {
                    return $cliente->getNombre() . ' ' . $cliente->getApellido() . ' (' . $cliente->getCedula() . ')';
                },
                'label' => 'Cliente',
            ])
            ->add('plan', EntityType::class, [
                'class' => MembresiaPlan::class,
                'choice_label' => 'nombrePlan',
                'label' => 'Plan',
            ])
            ->add('fechaInicio', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Fecha de inicio',
            ])
            ->add('fechaVencimiento', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Fecha de vencimiento',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MembresiaCliente::class,
        ]);
    }
}
