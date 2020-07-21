<?php

namespace App\Form;

use App\Entity\Program;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ProgramType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('summary')
            ->add('poster')
            ->add('API_id')
            ->add('year')
            ->add('runtime')
            ->add('awards')
            ->add('nb_seasons')
//            ->add('category')
            ->add('posterFile', VichFileType::class, [
                'required' => false,
                'allow_delete' => true, //not mandatory, default true
                'download_uri' => true, //not mandatory, default true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Program::class,
        ]);
    }
}
