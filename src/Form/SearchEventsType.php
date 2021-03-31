<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\SearchEvents;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchEventsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // TODO selectionner tous les campus ?
            ->add('campus', EntityType::class, [
                'label' => 'Campus : ',
                'class' => Campus::class,
                'choice_label' => 'name'
            ])
            ->add('keywords',TextType::class, [
                'label' => 'Le nom de la sortie contient : ',
                'label_attr'=> ['class'=> 'app-form-label'],
                'attr' => [
                    'class' => 'app-form-field',
                ],
                'required' => false,
            ])
            // TODO mettre un calendrier
            ->add('startDate',DateType::class, [
                'label' => 'Entre : ',
                'label_attr'=> ['class'=> 'app-form-label'],
                'required' => false,
            ])
            ->add('endDate',DateType::class, [
                'label' => 'et : ',
                'label_attr'=> ['class'=> 'app-form-label'],
                'required' => false,
            ])
            ->add('userIsOrganizer',CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur(trice)',
                'label_attr'=> ['class'=> 'app-form-label'],
                'required' => false,
            ])
            ->add('userIsRegistered',CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit(e)',
                'label_attr'=> ['class'=> 'app-form-label'],
                'required' => false,
            ])
            ->add('userIsNotRegistered',CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit(e) ',
                'label_attr'=> ['class'=> 'app-form-label'],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchEvents::class,
        ]);
    }
}
