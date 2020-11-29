<?php


namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('city')
            ->add('about')
            ->add('image', FileType::class, ['data_class' => null, 'required' => false])
            ->add('update', SubmitType::class, [
                'attr' => [
                    'class' => 'button is-primary'
                ]
            ]);
    }

}