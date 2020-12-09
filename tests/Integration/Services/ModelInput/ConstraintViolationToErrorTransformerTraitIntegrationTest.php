<?php

namespace Trikoder\JsonApiBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Trikoder\JsonApiBundle\Services\ModelInput\Traits\ConstraintViolationToErrorTransformer;
use Trikoder\JsonApiBundle\Tests\Resources\Model\ContactInfoModel;
use Trikoder\JsonApiBundle\Tests\Resources\Model\PhoneNumberModel;

class ConstraintViolationToErrorTransformerTraitIntegrationTest extends KernelTestCase
{
    public function testAttributeViolation()
    {
        $kernel = self::bootKernel();

        // prepare model
        $phoneNumber = new PhoneNumberModel();
        $phoneNumber->setAreaCode('12');
        $phoneNumber->setNumber('345678');
        $contactInfo = new ContactInfoModel();
        $contactInfo->setPhoneNumber($phoneNumber);
        $contactInfo->setLabel('too long for this');

        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('test.symfony.validator');

        /** @var ConstraintViolationListInterface $validation */
        $validation = $validator->validate($contactInfo);

        $this->assertCount(1, $validation);

        $trait = new ConstraintViolationToErrorTransformerTraitClass();
        $error = $trait->publicConvertViolationToError($validation->get(0));

        $this->assertEquals(409, $error->getStatus());
        // $this->assertNull($error->getCode()); // TODO - check if correct code is sent
        $this->assertEquals('This value is too long. It should have 10 characters or less.', $error->getTitle());
        $this->assertEquals('/data/attributes/label', $error->getSource()['pointer']);
        $this->assertEquals('too long for this', $error->getSource()['parameter']);
    }

    public function testDeepAttributeErrorFromForm()
    {
        $kernel = self::bootKernel();

        // prepare model
        $phoneNumber = new PhoneNumberModel();
        $phoneNumber->setAreaCode('1');
        $phoneNumber->setNumber('345678');
        $contactInfo = new ContactInfoModel();
        $contactInfo->setPhoneNumber($phoneNumber);
        $contactInfo->setLabel('valid');

        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('test.symfony.validator');

        /** @var ConstraintViolationListInterface $validation */
        $validation = $validator->validate($contactInfo);

        $this->assertCount(1, $validation);

        $trait = new ConstraintViolationToErrorTransformerTraitClass();
        $error = $trait->publicConvertViolationToError($validation->get(0));

        $this->assertEquals(409, $error->getStatus());
        // $this->assertNull($error->getCode()); // TODO - check if correct code is sent
        $this->assertEquals('This value is too short. It should have 2 characters or more.', $error->getTitle());
        $this->assertEquals('/data/attributes/phoneNumber/areaCode', $error->getSource()['pointer']);
        $this->assertEquals('1', $error->getSource()['parameter']);
    }

    public function testDeepAttributeErrorFromFormForGroup()
    {
        $kernel = self::bootKernel();

        // prepare model
        $phoneNumber = new PhoneNumberModel();
        $phoneNumber->setAreaCode('01');
        $phoneNumber->setNumber('345678');
        $contactInfo = new ContactInfoModel();
        $contactInfo->setPhoneNumber($phoneNumber);
        $contactInfo->setLabel('valid');

        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('test.symfony.validator');

        /** @var ConstraintViolationListInterface $validation */
        $validation = $validator->validate($contactInfo, null, 'alwaysInvalidPhoneNumber');

        $this->assertCount(1, $validation);

        $trait = new ConstraintViolationToErrorTransformerTraitClass();
        $error = $trait->publicConvertViolationToError($validation->get(0));

        $this->assertEquals(409, $error->getStatus());
        // $this->assertNull($error->getCode()); // TODO - check if correct code is sent
        $this->assertEquals('This is invalid', $error->getTitle());
        $this->assertEquals('/data/attributes/phoneNumber/number', $error->getSource()['pointer']);
        $this->assertEquals('345678', $error->getSource()['parameter']);
    }
}

class ConstraintViolationToErrorTransformerTraitClass
{
    use ConstraintViolationToErrorTransformer;

    public function publicConvertViolationToError(ConstraintViolationInterface $violation)
    {
        return $this->convertViolationToError($violation);
    }
}
