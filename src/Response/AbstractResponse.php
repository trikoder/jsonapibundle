<?php

namespace Trikoder\JsonApiBundle\Response;

/**
 * Class Response
 */
abstract class AbstractResponse
{
    /** @var array */
    private $meta = [];

    /** @var array */
    private $links = [];

    /**
     * AbstractResponse constructor.
     *
     * @param array $meta
     * @param array $links
     */
    public function __construct(array $meta = [], array $links = [])
    {
        $this->meta = $meta;
        $this->links = $links;
    }

    /**
     * @param $meta
     * @param $value
     *
     * @return self
     */
    public function addMeta($meta, $value): self
    {
        $this->meta[$meta] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * @return array
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @param $link
     * @param $value
     *
     * @return self
     */
    public function addLink($link, $value): self
    {
        $this->links[$link] = $value;

        return $this;
    }
}
