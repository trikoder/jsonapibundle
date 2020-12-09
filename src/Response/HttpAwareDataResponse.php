<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Response;

class HttpAwareDataResponse extends DataResponse
{
    /**
     * @var int|null
     */
    private $statusCode;

    /**
     * @var Header[]
     */
    private $headers;

    /**
     * @param Header[] $headers
     */
    public function __construct($data, array $meta = [], array $links = [], int $statusCode = null, array $headers = [])
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        parent::__construct($data, $meta, $links);
    }

    /**
     * @return int|null
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return Header[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
