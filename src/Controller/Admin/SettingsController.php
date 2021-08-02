<?php

namespace App\Controller\Admin;

use App\AppConsts;
use App\Controller\RestController;
use App\Entity\Settings;
use App\Form\SettingsType;
use App\Repository\SettingsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @Route("/settings")
 */
class SettingsController extends RestController
{
    /**
     * get
     * @Route("/get", methods="GET")
     * @SWG\Response(
     *     response=200, description=" ",
     *     @SWG\Schema(type="object", ref=@Model(type=Settings::class, groups={"Settings:get", "get", "File:min"}) )
     * )
     * @SWG\Response(response=404, description="Not Found")
     * @SWG\Tag(name="ADMIN_Settings")
     */
    public function getAction(Request $request, SettingsRepository $repository)
    {
        $entity = $repository->find(1);
        if(!$entity) {
            $em = $this->em();
            $entity = new Settings();
            $em->persist($entity);
            $em->flush();
        }

        return $this->json_response(
            $entity,
            AppConsts::CODE_200,
            ["Settings:get", "get", "File:min"]
        );
    }

    /**
     * Update
     * @Route("/update", methods={"PUT"})
     * @SWG\Parameter(
     *    name="Campaign", in="body", description=" ",
     *    @Model(type=Settings::class, groups={"Settings:post", "get"} )
     * )
     * @SWG\Response(
     *     response=200, description=" ",
     *     @SWG\Schema(type="object", ref=@Model(type=Settings::class, groups={"Settings:get", "get", "File:min"}) )
     * )
     * @SWG\Response(response=400, description="Invalid input")
     * @SWG\Response(response=404, description="Resource not found")
     * @SWG\Tag(name="ADMIN_Settings")
     */
    public function update(Request $request, SettingsRepository $repository)
    {
        $entity = $repository->find(1);

        $form = $this->createForm(SettingsType::class, $entity);
        $form->submit($request->request->all());
        $form->handleRequest($request);

        $res = $this->validate($entity, ['Settings:post']);
        if($res->type == AppConsts::SUCCESS) {

            $em = $this->em();
            $em->persist($entity);
            $em->flush();

            return $this->json_response(
                $entity,
                AppConsts::CODE_200,
                ["Settings:get", "get", "File:min"]
            );
        }

        return $this->json_response($res, AppConsts::CODE_INVALID_INPUT_400);
    }

}
