<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Trikoder\JsonApiBundle\Tests\Resources\Model\SimpleFileModel;

/**
 * Class SimpleFileType
 */
class SimpleFileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('simpleFileBinary', FileType::class, ['by_reference' => false]);
        $builder->add('title', TextType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SimpleFileModel::class,
        ]);
    }
}
