<?php

namespace Trikoder\JsonApiBundle\Response;

/**
 * Class DataResponse
 */
class DataResponse extends AbstractResponse
{
    private $data;

    /**
     * Response constructor.
     *
     * @param $data
     */
    public function __construct($data, array $meta = [], array $links = [])
    {
        $this->data = $data;
        parent::__construct($meta, $links);
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}
