<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Controller\Api\Demo;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Trikoder\JsonApiBundle\Controller\AbstractController as JsonApiController;
use Trikoder\JsonApiBundle\Config\Annotation as JsonApiConfig;
use Trikoder\JsonApiBundle\Controller\Traits\Actions\CreateTrait;
use Trikoder\JsonApiBundle\Controller\Traits\Actions\UpdateTrait;
use Trikoder\JsonApiBundle\Controller\Traits\UpdateActionTrait;
use Trikoder\JsonApiBundle\Services\ModelInput\CustomFormModelInputHandler;
use Trikoder\JsonApiBundle\Tests\Resources\Form\SimpleFileType;
use Trikoder\JsonApiBundle\Tests\Resources\Model\SimpleFileModel;

/**
 * @Route("/simple-file-upload")
 *
 * @JsonApiConfig\Config(
 *     modelClass="Trikoder\JsonApiBundle\Tests\Resources\Model\SimpleFileModel",
 *     repository="Trikoder\JsonApiBundle\Tests\Resources\Repository\SimpleFileModelRepository"
 * )
 */
class SimpleFileUploadController extends JsonApiController
{
    use CreateTrait;
    use UpdateActionTrait;

    /**
     * @Route("")
     * @Method("POST")
     *
     * @param Request $request
     * @return SimpleFileModel
     */
    public function uploadAction(Request $request)
    {
        return $this->createModelFromRequest($request);
    }

    /**
     * @inheritdoc
     */
    protected function getInputHandler()
    {
        $form = $this->createForm(SimpleFileType::class);
        return new CustomFormModelInputHandler($form);
    }

    /**
     * @return CustomFormModelInputHandler
     */
    protected function getCreateInputHandler()
    {
        return $this->getInputHandler();
    }

    /**
     * @return CustomFormModelInputHandler
     */
    protected function getUpdateInputHandler()
    {
        return $this->getInputHandler();
    }
}
