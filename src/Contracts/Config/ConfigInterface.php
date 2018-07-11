<?php

namespace Trikoder\JsonApiBundle\Contracts\Config;

/**
 * Interface ConfigInterface
 */
interface ConfigInterface
{
    /**
     * @return ApiConfigInterface
     */
    public function getApi();

    /**
     * @return CreateConfigInterface
     */
    public function getCreate();

    /**
     * @return IndexConfigInterface
     */
    public function getIndex();

    /**
     * @return UpdateConfigInterface
     */
    public function getUpdate();
}
