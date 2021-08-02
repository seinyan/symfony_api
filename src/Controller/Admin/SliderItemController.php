<?php

namespace App\Controller\Admin;

use App\AppConsts;
use App\Controller\RestController;
use App\Entity\SliderItem;
use App\Entity\File;
use App\Form\SliderItemType;
use App\Repository\SliderItemRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @Route("/slider_item")
 */
class SliderItemController extends RestController
{
    /**
     * List
     * @Route("/list", methods="GET")
     * @SWG\Parameter(name="page", in="query", type="integer", description="page num 1 2 3 4 ... ")
     * @SWG\Parameter(name="limit", in="query", type="integer", description="limit default 15 ")
     * @SWG\Response(
     *     response=200, description="",
     *     @SWG\Schema(type="array", @SWG\Items(ref=@Model(type=SliderItem::class, groups={"SliderItem:list", "get", "File:min" }))
     *    )
     * )
     * @SWG\Response(response=404, description="Not Found")
     * @SWG\Tag(name="ADMIN_SliderItem")
     */
    public function listAction(Request $request, SliderItemRepository $repository)
    {
        $qb = $repository->listAction();
        return $this->knpPaginationList(
            $qb,
            ["SliderItem:get", "get", "File:min"],
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 15)
        );
    }

    /**
     * get
     * @Route("/{id}", methods="GET")
     * @SWG\Response(
     *     response=200, description=" ",
     *     @SWG\Schema(type="object", ref=@Model(type=SliderItem::class, groups={"SliderItem:get", "get", "File:min"}) )
     * )
     * @SWG\Response(response=404, description="Not Found")
     * @SWG\Tag(name="ADMIN_SliderItem")
     */
    public function getAction(Request $request, SliderItemRepository $repository, $id)
    {
        $entity = $repository->find($id);
        if(!$entity) {
            return $this->json_response(null, AppConsts::CODE_NOT_FOUND_404);
        }

        return $this->json_response(
            $entity,
            AppConsts::CODE_200,
            ["SliderItem:get", "get", "File:min"]
        );
    }

    /**
     * create
     * @Route("/create", methods={"POST"})
     * @SWG\Parameter(
     *   name="create", in="body", description="  ",
     *   @Model(type=SliderItem::class, groups={"SliderItem:post", "post"})
     * ),
     * @SWG\Response(
     *   response=201, description="Created",
     *   @SWG\Schema(type="object", ref=@Model(type=SliderItem::class, groups={"SliderItem:get", "get", "File:min"}) )
     * )
     * @SWG\Response(response=400, description="Invalid input")
     * @SWG\Tag(name="ADMIN_SliderItem")
     */
    public function create(Request $request, SliderItemRepository $contactRepository)
    {
        $entity = new SliderItem();
        $form = $this->createForm(SliderItemType::class, $entity);
        $form->submit($request->request->all());
        $form->handleRequest($request);

        $res = $this->validate($entity, ['SliderItem:post']);
        if($res->type == AppConsts::SUCCESS) {

            $em = $this->em();
            $em->persist($entity);
            $em->flush();

            return $this->json_response(
                $entity,
                AppConsts::CODE_CREATED_201,
                ["SliderItem:get", "get", "File:min"]
            );
        }

        return $this->json_response($res, AppConsts::CODE_INVALID_INPUT_400);
    }

    /**
     * Update
     * @Route("/update/{id}", methods={"PUT"})
     * @SWG\Parameter(
     *    name="Campaign", in="body", description=" ",
     *    @Model(type=SliderItem::class, groups={"SliderItem:post", "post"})
     * ),
     * @SWG\Response(
     *   response=200, description=" ",
     *   @SWG\Schema(type="object", ref=@Model(type=SliderItem::class, groups={"SliderItem:get", "get", "File:min"})
     *   )
     * )
     * @SWG\Response(response=400, description="Invalid input")
     * @SWG\Response(response=404, description="Resource not found")
     * @SWG\Tag(name="ADMIN_SliderItem")
     */
    public function update(Request $request, SliderItemRepository $repository, $id)
    {
        /** @var \App\Entity\SliderItem $entity */
        $entity = $repository->find($id);
        if(!$entity) {
            return $this->json_response(null, AppConsts::CODE_NOT_FOUND_404);
        }

        $form = $this->createForm(SliderItemType::class, $entity);
        $form->submit($request->request->all());
        $form->handleRequest($request);

        $res = $this->validate($entity, ['SliderItem:post']);
        if($res->type == AppConsts::SUCCESS) {

            $em = $this->em();
            $em->persist($entity);
            $em->flush();

            return $this->json_response(
                $entity,
                AppConsts::CODE_200,
                ["News:get", "get", "File:min"]
            );
        }

        return $this->json_response($res, AppConsts::CODE_INVALID_INPUT_400);
    }

    /**
     * Delete
     * @Route("/delete/{id}", methods="DELETE")
     * @SWG\Response(response=204, description="Resource deleted")
     * @SWG\Response(response=404,description="Resource not found")
     * @SWG\Tag(name="ADMIN_SliderItem")
     */
    public function delete(Request $request, SliderItemRepository $repository, $id)
    {
        /** @var \App\Entity\SliderItem $entity */
        $entity = $repository->find($id);
        if(!$entity) {
            return $this->json_response(null, AppConsts::CODE_NOT_FOUND_404);
        }

        $em = $this->em();
        $em->remove($entity);
        $em->flush();

        return $this->json_response(null, AppConsts::CODE_DELETED_204);
    }

    /**
     * Update
     * @Route("/is_publish/{id}", methods={"PUT"})
     * @SWG\Parameter( name="is_publish", in="formData", type="string", description="is_active - true || false")
     * @SWG\Response(response=200, description="")
     * @SWG\Response(response=400, description="Invalid input")
     * @SWG\Response(response=404, description="Resource not found")
     * @SWG\Tag(name="ADMIN_SliderItem")
     */
    public function isPublish(Request $request, SliderItemRepository $repository, $id)
    {
        $entity = $repository->find($id);
        if(!$entity) {
            return $this->json_response(null, AppConsts::CODE_NOT_FOUND_404);
        }

        $is_active = $request->request->getBoolean('is_publish');
        $entity->setIsPublish($is_active);

        $em = $this->em();
        $em->persist($entity);
        $em->flush();

        return $this->json_response([], AppConsts::CODE_200, ['SliderItem:get', 'get']);
    }
}
