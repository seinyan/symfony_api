<?php

namespace App\Controller\Pub;

use App\AppConsts;
use App\Controller\RestController;
use App\Entity\RequestForm;
use App\Entity\Settings;
use App\Entity\Subscriber;
use App\Form\RequestFormType;
use App\Form\SubscriberType;
use App\Repository\RequestFormRepository;
use App\Repository\SettingsRepository;
use App\Repository\SubscriberRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @Route("/mix")
 */
class MixController extends RestController
{
    /**
     * get
     * @Route("/settings", methods="GET")
     * @SWG\Response(
     *     response=200, description=" ",
     *     @SWG\Schema(type="object", ref=@Model(type=Settings::class, groups={"Settings:get", "get", "File:min"}) )
     * )
     * @SWG\Response(response=404, description="Not Found")
     * @SWG\Tag(name="PUB_Mix")
     */
    public function getSettings(Request $request, SettingsRepository $repository)
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
            ["Settings:get", "get", "list", "File:min"]
        );
    }

    /**
     * create
     * @Route("/request_form", methods={"POST"})
     * @SWG\Parameter(
     *   name="create", in="body", description="  ",
     *   @Model(type=RequestForm::class, groups={"RequestForm:post", "post"})
     * ),
     * @SWG\Response(
     *   response=201, description="Created",
     *   @SWG\Schema(type="object", ref=@Model(type=RequestForm::class, groups={"RequestForm:get", "get", "File:min"}) )
     * )
     * @SWG\Response(response=400, description="Invalid input")
     * @SWG\Tag(name="PUB_Mix")
     */
    public function requestForm(Request $request, RequestFormRepository $contactRepository)
    {
        $entity = new RequestForm();
        $form = $this->createForm(RequestFormType::class, $entity);
        $form->submit($request->request->all());
        $form->handleRequest($request);

        $res = $this->validate($entity, ['RequestForm:post']);
        if($res->type == AppConsts::SUCCESS) {

            $entity->setCreatedAt(new \DateTime());
            $entity->setStatus(RequestForm::STATUS_NEW);

            $em = $this->em();
            $em->persist($entity);
            $em->flush();

            return $this->json_response(
                $entity,
                AppConsts::CODE_CREATED_201,
                ["RequestForm:get",]
            );
        }

        return $this->json_response($res, AppConsts::CODE_INVALID_INPUT_400);
    }

    /**
     * create
     * @Route("/subscriber", methods={"POST"})
     * @SWG\Parameter(
     *   name="create", in="body", description="  ",
     *   @Model(type=Subscriber::class, groups={"Subscriber:post", "post"})
     * ),
     * @SWG\Response(
     *   response=201, description="Created",
     *   @SWG\Schema(type="object", ref=@Model(type=Subscriber::class, groups={"Subscriber:get", "get", "File:min"}) )
     * )
     * @SWG\Response(response=400, description="Invalid input")
     * @SWG\Tag(name="PUB_Mix")
     */
    public function subscriber(Request $request, SubscriberRepository $contactRepository)
    {
        $entity = new Subscriber();
        $form = $this->createForm(SubscriberType::class, $entity);
        $form->submit($request->request->all());
        $form->handleRequest($request);

        $res = $this->validate($entity, ['Subscriber:post']);
        if($res->type == AppConsts::SUCCESS) {

            $em = $this->em();
            $em->persist($entity);
            $em->flush();

            return $this->json_response(
                $entity,
                AppConsts::CODE_CREATED_201,
                ["Subscriber:list", "get"]
            );
        }

        return $this->json_response($res, AppConsts::CODE_INVALID_INPUT_400);
    }

}
