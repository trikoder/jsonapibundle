<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Model;

use Symfony\Component\HttpFoundation\File\File;

/**
 * Class SimpleFileModel
 */
class SimpleFileModel
{
    private $name;

    private $extension;

    private $title;

    /**
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
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
        $this->name = str_replace('.' . $this->extension, '', $file->getFilename());
    }

    public function hasSimpleFileBinary()
    {
        return false;
    }

    /**
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return $this
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }
}
