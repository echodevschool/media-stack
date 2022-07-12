<?php

namespace App\Form;

use App\Entity\Music;
use App\Entity\MusicGenre;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class MusicFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Наименование'
            ])
            ->add('artist', TextType::class,[
                'label' => 'Исполнитель'
            ])
            ->add('album', TextType::class,[
                'label' => 'Альбом'
            ])
            ->add('m_wallpeper', FileType::class, [
                'label' => 'Обложка',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5000k',
                        'mimeTypes' => [
                            'image/*'
                        ],
                        'mimeTypesMessage' => 'file is not valid'
                    ])
                ]

            ])
//            ->add('wallpaper', TextType::class,[
//                'label' => 'Обложка'
//            ])
            ->add('genre', EntityType::class, [
                'class' => MusicGenre::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('m');
                },
                'choice_label' => 'name',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Сохранить'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Music::class,
        ]);
    }
}
