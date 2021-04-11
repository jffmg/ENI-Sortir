<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userName', TextType::class, [
                'label' => 'Pseudo : ',
                'label_attr' => ['class' => 'app-form-label'],
                'attr' => [
                    'class' => 'app-form-field app-update-field',
                ]
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom : ',
                'label_attr' => ['class' => 'app-form-label'],
                'attr' => [
                    'class' => 'app-form-field app-update-field',
                ]
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom : ',
                'label_attr' => ['class' => 'app-form-label'],
                'attr' => [
                    'class' => 'app-form-field app-update-field',
                ]
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone : ',
                'label_attr' => ['class' => 'app-form-label'],
                'attr' => [
                    'class' => 'app-form-field app-update-field',
                ],
                'required' => false,
            ])
            ->add('mail', TextType::class, [
                'label' => 'Mail : ',
                'label_attr' => ['class' => 'app-form-label'],
                'attr' => [
                    'class' => 'app-form-field app-update-field',
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe doivent être identiques',
                'options' => ['attr' => ['class' => 'app-password-field']],
                'required' => true,
                'first_options' => ['label' => 'Mot de passe : '],
                'second_options' => ['label' => 'Confirmation : '],
            ])
            ->add('pictureFile', VichImageType::class, [
                'label' => 'Photo de profil : ',
                'label_attr' => ['class' => 'app-form-label'],
                'required' => false,
                'allow_delete' => false,
                /*'delete_label' => 'Supprimer',*/
                /*'download_label' => 'Télécharger',*/
                'download_uri' => false,
                'image_uri' => false,
                'asset_helper' => true,
                'attr' => [
                    'class' => 'app-form-field app-update-field',
                ]
            ])
        ->setMethod('POST');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
