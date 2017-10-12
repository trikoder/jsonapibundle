<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Model;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class SimpleFileModel
 * @package Trikoder\JsonApiBundle\Tests\Resources\Model
 */
class SimpleFileModel
{
    private $name;

    private $extension;

    private $title;

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setSimpleFileBinary(File $file)
    {
        $this->extension = $file->getExtension();
        $this->name = str_replace('.'.$this->extension, '', $file->getFilename());
    }

    public function hasSimpleFileBinary()
    {
        return false;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param mixed $extension
     * @return $this
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
        return $this;
    }

}
