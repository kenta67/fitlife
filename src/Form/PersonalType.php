<?php

namespace App\Form;

use App\Entity\Personal;
use App\Entity\Rol;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PersonalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre completo',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('usuario', TextType::class, [
                'label' => 'Usuario',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('contrasena', PasswordType::class, [
                'label' => 'Contraseña',
                'required' => !$options['is_edit'], // En edición no es obligatorio
                'mapped' => false, // No mapear automáticamente a la entidad
                'constraints' => $options['is_edit'] ? [] : [
                    new NotBlank(['message' => 'La contraseña no puede estar vacía']),
                    new Length(['min' => 4, 'minMessage' => 'Mínimo 4 caracteres']),
                ],
                'attr' => ['class' => 'form-control', 'autocomplete' => 'off'],
            ])
            ->add('rol', EntityType::class, [
                'class' => Rol::class,
                'choice_label' => 'nombre',
                'label' => 'Rol',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('estado', CheckboxType::class, [
                'label' => 'Activo',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personal::class,
            'is_edit' => false, // Opción para saber si es edición
        ]);
    }
}