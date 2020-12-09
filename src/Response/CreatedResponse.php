<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Response;

use Symfony\Component\HttpFoundation\Response;

final class CreatedResponse extends HttpAwareDataResponse
{
    public function __construct($data, array $meta = [], array $links = [], string $location = null, array $headers = [])
    {
        if (null !== $location) {
            $headers[] = new Header('Location', $location);
        }

        parent::__construct($data, $meta, $links, Response::HTTP_CREATED, $headers);
    }
}
