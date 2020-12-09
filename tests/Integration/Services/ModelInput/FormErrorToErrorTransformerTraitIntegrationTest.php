<?php

namespace Trikoder\JsonApiBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormInterface;
use Trikoder\JsonApiBundle\Services\ModelInput\Traits\FormErrorToErrorTransformer;
use Trikoder\JsonApiBundle\Tests\Resources\Form\ContactInfoType;
use Trikoder\JsonApiBundle\Tests\Resources\Form\ContactInfoTypeWithGroup;
use Trikoder\JsonApiBundle\Tests\Resources\Form\PhoneNumberType;

class FormErrorToErrorTransformerTraitIntegrationTest extends KernelTestCase
{
    public function testAttributeErrorFromForm()
    {
        $kernel = self::bootKernel();

        /** @var FormInterface $form */
        $form = $kernel->getContainer()->get('test.symfony.form_factory')->create(ContactInfoType::class);

        $form->submit([
            'label' => 'too long for this',
            'phoneNumber' => [
                'areaCode' => '12',
                'number' => '345678',
                'intNumber' => 34343,
                'numberWithValidationOnlyOnForm' => '123',
            ],
        ]);

        $this->assertFalse($form->isValid());

        $formErrors = $form->getErrors(true);

        $this->assertEquals(1, $formErrors->count());

        $trait = new FormErrorToErrorTransformerTraitClass();
        $error = $trait->publicFormErrorToError($formErrors->current());

        $this->assertEquals(409, $error->getStatus());
        $this->assertNull($error->getCode());
        $this->assertEquals('This value is too long. It should have 10 characters or less.', $error->getTitle());
        $this->assertEquals('/data/attributes/label', $error->getSource()['pointer']);
        $this->assertEquals('too long for this', $error->getSource()['parameter']);
    }

    public function testDeepAttributeErrorFromForm()
    {
        $kernel = self::bootKernel();

        /** @var FormInterface $form */
        $form = $kernel->getContainer()->get('test.symfony.form_factory')->create(ContactInfoType::class);

        $form->submit([
            'label' => 'valid',
            'phoneNumber' => [
                'areaCode' => '1',
                'number' => '345678',
                'intNumber' => 34343,
                'numberWithValidationOnlyOnForm' => '123',
            ],
        ]);

        $this->assertFalse($form->isValid());

        $formErrors = $form->getErrors(true);

        $this->assertEquals(1, $formErrors->count());

        $trait = new FormErrorToErrorTransformerTraitClass();
        $error = $trait->publicFormErrorToError($formErrors->current());

        $this->assertEquals(409, $error->getStatus());
        $this->assertNull($error->getCode());
        $this->assertEquals('This value is too short. It should have 2 characters or more.', $error->getTitle());
        $this->assertEquals('/data/attributes/phoneNumber/areaCode', $error->getSource()['pointer']);
        $this->assertEquals('1', $error->getSource()['parameter']);
    }

    public function testDeepAttributeErrorFromFormForGroup()
    {
        $kernel = self::bootKernel();

        /** @var FormInterface $form */
        $form = $kernel->getContainer()->get('test.symfony.form_factory')->create(ContactInfoTypeWithGroup::class);

        $form->submit([
            'label' => 'valid',
            'phoneNumber' => [
                'areaCode' => '1',
                'number' => '345678',
                'intNumber' => 34343,
                'numberWithValidationOnlyOnForm' => '123',
            ],
        ]);

        $this->assertFalse($form->isValid());

        $formErrors = $form->getErrors(true);

        $this->assertEquals(1, $formErrors->count());

        $trait = new FormErrorToErrorTransformerTraitClass();
        $error = $trait->publicFormErrorToError($formErrors->current());

        $this->assertEquals(409, $error->getStatus());
        $this->assertNull($error->getCode());
        $this->assertEquals('This is invalid', $error->getTitle());
        $this->assertEquals('/data/attributes/phoneNumber/number', $error->getSource()['pointer']);
        $this->assertEquals('345678', $error->getSource()['parameter']);
    }

    public function testFormTypeValidationCalculatingCorrectPath()
    {
        $kernel = self::bootKernel();

        /** @var FormInterface $form */
        $form = $kernel->getContainer()->get('test.symfony.form_factory')->create(ContactInfoType::class);

        $form->submit([
            'label' => 'valid',
            'phoneNumber' => [
                'areaCode' => '12',
                'number' => '345678',
                'intNumber' => 'abcdef',  // this is IntegerType in @see PhoneNumberType, so this will trigger error
                'numberWithValidationOnlyOnForm' => '123',
            ],
        ]);

        $this->assertFalse($form->isValid());

        $formErrors = $form->getErrors(true);

        $this->assertEquals(1, $formErrors->count());

        $trait = new FormErrorToErrorTransformerTraitClass();
        $error = $trait->publicFormErrorToError($formErrors->current());

        $this->assertEquals(409, $error->getStatus());
        $this->assertNull($error->getCode());
        $this->assertEquals('This value is not valid.', $error->getTitle());
        $this->assertEquals('/data/attributes/phoneNumber/intNumber', $error->getSource()['pointer']);
        $this->assertEquals('abcdef', $error->getSource()['parameter']);
    }

    public function testSymfonyFormTypeErrorPathBeingCorrectlyCalculatedForConstraintDefinedOnForm()
    {
        $kernel = self::bootKernel();

        /** @var FormInterface $form */
        $form = $kernel->getContainer()->get('test.symfony.form_factory')->create(ContactInfoType::class);

        $form->submit([
            'label' => 'valid',
            'phoneNumber' => [
                'areaCode' => '12',
                'number' => '345678',
                'intNumber' => 123,
                'numberWithValidationOnlyOnForm' => '1', // this has constraints defined on @see PhoneNumberType
            ],
        ]);

        $this->assertFalse($form->isValid());

        $formErrors = $form->getErrors(true);

        $this->assertEquals(1, $formErrors->count());

        $trait = new FormErrorToErrorTransformerTraitClass();
        $error = $trait->publicFormErrorToError($formErrors->current());

        $this->assertEquals(409, $error->getStatus());
        $this->assertNull($error->getCode());
        $this->assertEquals('This value is too short. It should have 3 characters or more.', $error->getTitle());
        $this->assertEquals('/data/attributes/phoneNumber/numberWithValidationOnlyOnForm', $error->getSource()['pointer']);
        $this->assertEquals('1', $error->getSource()['parameter']);
    }
}

class FormErrorToErrorTransformerTraitClass
{
    use FormErrorToErrorTransformer;

    public function publicFormErrorToError($violation)
    {
        return $this->convertFormErrorToError($violation);
    }
}
