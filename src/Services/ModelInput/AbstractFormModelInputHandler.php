<?php

namespace Trikoder\JsonApiBundle\Services\ModelInput;

use Doctrine\Common\Persistence\Proxy;
use Symfony\Component\Form\FormInterface;
use Trikoder\JsonApiBundle\Contracts\ModelTools\ModelInputHandlerInterface;

abstract class AbstractFormModelInputHandler implements ModelInputHandlerInterface
{
    /**
     * @var object
     */
    protected $model;

    /**
     * @var string
     */
    protected $modelClass;

    /**
     * @return FormInterface
     */
    abstract public function getForm();

    /**
     * @param object $model
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function forModel($model)
    {
        // TODO - we really do not wanna change model types once set? if we use generic handler that caluclates form on the fly
        if (null !== $this->model && null !== $this->modelClass) {
            if (!($model instanceof $this->modelClass)) {
                throw new \Exception('change of model type is not allowed');
            }
        }
        $this->model = $model;

        // make sure we are not working with doctrine proxy
        if ($this->model instanceof Proxy) {
            $this->modelClass = get_parent_class($this->model);
        } else {
            $this->modelClass = \get_class($this->model);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function handle(array $input)
    {
        $form = $this->getForm();
        $form->submit($input, false); // false is important to allow patch
        // TODO - should we only update result if valid?
        $this->model = $form->getData();

        return $this;
    }

    /**
     * @return object model with inputs applied by handler's rules
     */
    public function getResult()
    {
        return $this->model;
    }
}
