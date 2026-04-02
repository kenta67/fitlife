<?php

namespace App\Form;

use App\Entity\Pago;
use App\Entity\MembresiaCliente;
use App\Entity\Personal;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PagoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Selección de la membresía del cliente
            ->add('membresiaCliente', EntityType::class, [
                'class' => MembresiaCliente::class,
                'choice_label' => function ($mc) {
                    return $mc->getCliente()->getNombre() . ' ' . $mc->getCliente()->getApellido() . ' - ' . $mc->getPlan()->getNombrePlan();
                },
                'label' => 'Membresía del Cliente',
            ])
            // Selección del personal que registra el pago
            ->add('personal', EntityType::class, [
                'class' => Personal::class,
                'choice_label' => 'nombre',
                'label' => 'Registrado por',
            ])
            ->add('monto', MoneyType::class, [
                'label' => 'Monto (Bs)',
                'currency' => false,
                'scale' => 2,
            ])
            ->add('fechaPago', DateType::class, [
                'label' => 'Fecha de Pago',
                'widget' => 'single_text',
            ])
            ->add('metodoPago', ChoiceType::class, [
                'label' => 'Método de Pago',
                'choices' => [
                    'Efectivo' => 'Efectivo',
                    'Transferencia' => 'Transferencia',
                    'Tarjeta' => 'Tarjeta',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pago::class,
        ]);
    }
}
