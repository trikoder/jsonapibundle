<?php

namespace Trikoder\JsonApiBundle\Response;

/**
 * Class DataResponse
 */
class DataResponse extends AbstractResponse
{
    /** @var mixed */
    private $data;

    /**
     * Response constructor.
     *
     * @param $data
     * @param array $meta
     * @param array $links
     */
    public function __construct($data, array $meta = [], array $links = [])
    {
        $this->data = $data;
        parent::__construct($meta, $links);
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}
