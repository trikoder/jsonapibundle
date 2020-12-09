<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Services\RequestDecoder;

use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Trikoder\JsonApiBundle\Contracts\RequestBodyValidatorInterface;

final class SymfonyValidatorAdapter implements RequestBodyValidatorInterface
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

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

        return $this->validator->validate($body, new Collection([
            'data' => new Required([
                new Collection([
                    'type' => [
                        new Required($this->createMemberNameConstraints()),
                    ],
                    // optional because for instance on PATCH it's required, but on POST it's not
                    'id' => new Optional($this->createMemberNameConstraints()),
                    'attributes' => new Optional(),
                    'relationships' => new Optional([
                        new All([
                            new Collection([
                                'data' => [
                                    new Required(),
                                ],
                            ]),
                            new Callback(function ($value, ExecutionContextInterface $context, $payload) {
                                $value = $value['data'];
                                if (null === $value) {
                                    return;
                                }

                                $message = 'Not a valid resource identifier';
                                if ($this->isAssociativeArray($value)) {
                                    if (!$this->isValidResourceIdentifier($value)) {
                                        $context->addViolation($message);
                                    }

                                    return;
                                }

                                if (\is_array($value)) {
                                    foreach ($value as $index => $item) {
                                        if (!$this->isValidResourceIdentifier($item)) {
                                            $context->buildViolation($message)
                                                ->atPath($index)
                                                ->addViolation();
                                        }
                                    }
                                }
                            }),
                        ]),
                    ]),
                ]),
            ]),
        ]));
    }

    /**
     * @todo this isn't quite right, could be improved, but will do for now
     *
     * @see https://jsonapi.org/format/#document-member-names-allowed-characters
     */
    private function createMemberNameConstraints()
    {
        return [
            /*
             * @see https://gitlab.trikoder.net/trikoder/jsonapibundle/merge_requests/101#note_244436
             * new Type('string'),
             */
            new NotBlank(),
        ];
    }

    private function isValidResourceIdentifier(array $input)
    {
        return !empty($input['id']) && !empty($input['type']);
    }

    private function isAssociativeArray(array $input)
    {
        return \count(array_filter(array_keys($input), 'is_string')) > 0;
    }
}
