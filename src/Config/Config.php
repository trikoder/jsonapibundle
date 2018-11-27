<?php

namespace Trikoder\JsonApiBundle\Config;

use Trikoder\JsonApiBundle\Contracts\Config\ApiConfigInterface;
use Trikoder\JsonApiBundle\Contracts\Config\ConfigInterface;
use Trikoder\JsonApiBundle\Contracts\Config\CreateConfigInterface;
use Trikoder\JsonApiBundle\Contracts\Config\DeleteConfigInterface;
use Trikoder\JsonApiBundle\Contracts\Config\IndexConfigInterface;
use Trikoder\JsonApiBundle\Contracts\Config\UpdateConfigInterface;

/**
 * Class Config
 */
final class Config implements ConfigInterface
{
    /**
     * @var ApiConfigInterface
     */
    private $api;

    /**
     * @var CreateConfigInterface
     */
    private $create;

    /**
     * @var IndexConfigInterface
     */
    private $index;

    /**
     * @var UpdateConfigInterface
     */
    private $update;

    /**
     * @var DeleteConfigInterface
     */
    private $delete;

    /**
     * Config constructor.
     */
    public function __construct(
        ApiConfigInterface $api,
        CreateConfigInterface $create,
        IndexConfigInterface $index,
        UpdateConfigInterface $update,
        DeleteConfigInterface $delete
    ) {
        $this->api = $api;
        $this->create = $create;
        $this->index = $index;
        $this->update = $update;
        $this->delete = $delete;
    }

    /**
     * @return ApiConfigInterface
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * @return CreateConfigInterface
     */
    public function getCreate()
    {
        return $this->create;
    }

    /**
     * @return IndexConfigInterface
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @return UpdateConfigInterface
     */
    public function getUpdate()
    {
        return $this->update;
    }

    /**
     * @return DeleteConfigInterface
     */
    public function getDelete()
    {
        return $this->delete;
    }
}
