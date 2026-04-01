<?php

namespace App\Form;

use App\Entity\Clase;
use App\Entity\Personal;
use App\Repository\PersonalRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, ['label' => 'Nombre de la clase'])
            ->add('descripcion', TextareaType::class, [
                'label' => 'Descripción',
                'required' => false,
                'attr' => ['rows' => 3],
            ])
            ->add('capacidadMax', IntegerType::class, [
                'label' => 'Capacidad máxima',
                'attr' => ['min' => 1],
            ])
            ->add('horario', TextType::class, [
                'label' => 'Horario',
                'required' => false,
                'attr' => ['placeholder' => 'Ej: Lun-Mié-Vie 18:00'],
            ])
            ->add('instructor', EntityType::class, [
                'class' => Personal::class,
                'choice_label' => 'nombre',
                'label' => 'Instructor',
                'query_builder' => function (PersonalRepository $repo) {
                    return $repo->createQueryBuilder('p')
                        ->join('p.rol', 'r')
                        ->where('r.nombre = :rol')
                        ->setParameter('rol', 'instructor');
                },
                'placeholder' => 'Seleccione un instructor',
            ])
            ->add('estado', CheckboxType::class, [
                'label' => 'Activa',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Clase::class,
        ]);
    }
}