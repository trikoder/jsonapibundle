<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Services\RequestDecoder;

use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Trikoder\JsonApiBundle\Contracts\RequestBodyValidatorInterface;

final class RelationshipValidatorAdapter implements RequestBodyValidatorInterface
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Validate:
     *    {
     *       "data": [
     *           { "type": "tags", "id": "2" },
     *           { "type": "tags", "id": "3" }
     *       ]
     *   }
     */
    public function validate(array $body)
    {
        /*
         * Symfony validator doesn't have support for multiple type validation, i.e. validating that "data" is either
         * a collection or null, so we have to do this explicit check up front.
         * This got implemented in 4.4, @see https://github.com/symfony/symfony/pull/31351
         */
        if (\array_key_exists('data', $body) && null === $body['data']) {
            return new ConstraintViolationList();
        }

        $validation = $this->validator->validate($body, new Collection([
            'data' => new Required([
                new Type('array'),
                new NotBlank(),
                new All([
                    new Type('array'),
                    new Collection([
                        'type' => new Required($this->createMemberNameConstraints()),
                        'id' => new Required($this->createMemberNameConstraints()),
                    ]),
                ]),
            ]),
        ]));

        return $validation;
    }

    /**
     * @todo this isn't quite right, could be improved, but will do for now
     *
     * @see https://jsonapi.org/format/#document-member-names-allowed-characters
     */
    private function createMemberNameConstraints()
    {
        return [
            new NotBlank(),
            new Type('string'),
        ];
    }
}
