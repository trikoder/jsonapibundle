<?php

namespace Trikoder\JsonApiBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Trikoder\JsonApiBundle\Services\ModelInput\GenericFormModelInputHandler;
use Trikoder\JsonApiBundle\Services\ModelInput\ModelMetaDataFactory;
use Trikoder\JsonApiBundle\Services\ModelInput\ModelToolsFactory;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\GenericModel;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\User;

class GenericFormInputHandlerTest extends KernelTestCase
{
    public function testDoctrineModelMetaData()
    {
        $kernel = static::bootKernel();

        /** @var ModelMetaDataFactory $metaDataFactory */
        $metaDataFactory = $kernel->getContainer()->get('test.trikoder.jsonapi.model_meta_data_factory');

        $entity = new User();
        $meta = $metaDataFactory->getMetaDataForModel(User::class);

        /** @var GenericFormModelInputHandler $inputHandler */
        $inputHandler = $kernel->getContainer()->get(ModelToolsFactory::class)->createInputHandler($entity);
        $form = $inputHandler->getForm();

        $this->assertInstanceOf(IntegerType::class, $form->get('id')->getConfig()->getType()->getInnerType());
        $this->assertInstanceOf(EmailType::class, $form->get('email')->getConfig()->getType()->getInnerType());
        $this->assertInstanceOf(CheckboxType::class, $form->get('active')->getConfig()->getType()->getInnerType());
    }

    public function testGenericModelMetaData()
    {
        $kernel = static::bootKernel();

        /** @var ModelMetaDataFactory $metaDataFactory */
        $metaDataFactory = $kernel->getContainer()->get('test.trikoder.jsonapi.model_meta_data_factory');

        $entity = new GenericModel();
        $meta = $metaDataFactory->getMetaDataForModel(GenericModel::class);

        /** @var GenericFormModelInputHandler $inputHandler */
        $inputHandler = $kernel->getContainer()->get(ModelToolsFactory::class)->createInputHandler($entity);
        $form = $inputHandler->getForm();

        $this->assertInstanceOf(TextType::class, $form->get('id')->getConfig()->getType()->getInnerType());
        $this->assertInstanceOf(TextType::class, $form->get('title')->getConfig()->getType()->getInnerType());
        $this->assertInstanceOf(CheckboxType::class, $form->get('isActive')->getConfig()->getType()->getInnerType());
        $this->assertInstanceOf(CheckboxType::class, $form->get('approved')->getConfig()->getType()->getInnerType());
        $this->assertInstanceOf(TextType::class, $form->get('description')->getConfig()->getType()->getInnerType());
        $this->assertInstanceOf(CheckboxType::class, $form->get('canPost')->getConfig()->getType()->getInnerType());
        $this->assertInstanceOf(DateTimeType::class, $form->get('date')->getConfig()->getType()->getInnerType());
        $this->assertInstanceOf(CollectionType::class, $form->get('dependentArray')->getConfig()->getType()->getInnerType());
    }
}
