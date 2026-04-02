<?php

namespace App\Form;

use App\Entity\Cliente;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClienteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre',
                'attr' => ['placeholder' => 'Nombre del cliente'],
            ])
            ->add('apellido', TextType::class, [
                'label' => 'Apellido',
                'attr' => ['placeholder' => 'Apellido del cliente'],
            ])
            ->add('cedula', TextType::class, [
                'label' => 'Cédula',
                'attr' => ['placeholder' => 'Número de identificación'],
            ])
            ->add('correo', EmailType::class, [
                'label' => 'Correo electrónico',
                'required' => false,
                'attr' => ['placeholder' => 'cliente@ejemplo.com'],
            ])
            ->add('telefono', TelType::class, [
                'label' => 'Teléfono',
                'required' => false,
                'attr' => ['placeholder' => 'Ej: 59171234567'],
            ])
            ->add('estado', null, [
                'label' => 'Activo',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cliente::class,
        ]);
    }
}