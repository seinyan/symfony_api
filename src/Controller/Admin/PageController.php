<?php

namespace App\Controller\Admin;

use App\AppConsts;
use App\Controller\RestController;
use App\Entity\Page;
use App\Form\PageType;
use App\Repository\PageRepository;
use App\Services\SlugService;
use App\Utils\Slug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @Route("/page")
 */
class PageController extends RestController
{
    /**
     * List
     * @Route("/list", methods="GET")
     * @SWG\Parameter(name="page", in="query", type="integer", description="page num 1 2 3 4 ... ")
     * @SWG\Parameter(name="limit", in="query", type="integer", description="limit default 15 ")
     * @SWG\Response(
     *     response=200, description="",
     *     @SWG\Schema(type="array", @SWG\Items(ref=@Model(type=Page::class, groups={"Page:list", "get", "File:min" }))
     *    )
     * )
     * @SWG\Response(response=404, description="Not Found")
     * @SWG\Tag(name="ADMIN_Page")
     */
    public function listAction(Request $request, PageRepository $repository)
    {
        $qb = $repository->listAction();
        return $this->knpPaginationList(
            $qb,
            ["Page:list", "Page:get", "get", "File:min"],
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 15)
        );
    }

    /**
     * get
     * @Route("/{id}", methods="GET")
     * @SWG\Response(
     *     response=200, description=" ",
     *     @SWG\Schema(type="object", ref=@Model(type=Page::class, groups={"Page:get", "get", "File:min"}) )
     * )
     * @SWG\Response(response=404, description="Not Found")
     * @SWG\Tag(name="ADMIN_Page")
     */
    public function getAction(Request $request ,PageRepository $repository, $id)
    {
        $entity = $repository->find($id);
        if(!$entity) {
            return $this->json_response(null, AppConsts::CODE_NOT_FOUND_404);
        }

        return $this->json_response(
            $entity,
            AppConsts::CODE_200,
            ["Page:get", "get", "File:min"]
        );
    }

    /**
     * create
     * @Route("/create", methods={"POST"})
     * @SWG\Parameter(
     *   name="create", in="body", description="  ",
     *   @Model(type=Page::class, groups={"Page:post", "post"})
     * ),
     * @SWG\Response(
     *   response=201, description="Created",
     *   @SWG\Schema(type="object", ref=@Model(type=Page::class, groups={"Page:get", "get", "File:min"}) )
     * )
     * @SWG\Response(response=400, description="Invalid input")
     * @SWG\Tag(name="ADMIN_Page")
     */
    public function create(Request $request, PageRepository $contactRepository, Slug $slug)
    {
        $entity = new Page();
        $form = $this->createForm(PageType::class, $entity);
        $form->submit($request->request->all());
        $form->handleRequest($request);

        $entity->setSlug($slug->textToSlug($entity->getTitle()));

        $res = $this->validate($entity, ['Page:post']);
        if($res->type == AppConsts::SUCCESS) {

            $em = $this->em();
            $em->persist($entity);
            $em->flush();

            return $this->json_response(
                $entity,
                AppConsts::CODE_CREATED_201,
                ["Page:get", "get", "File:min"]
            );
        }

        return $this->json_response($res, AppConsts::CODE_INVALID_INPUT_400);
    }

    /**
     * Update
     * @Route("/update/{id}", methods={"PUT"})
     * @SWG\Parameter(
     *    name="Campaign", in="body", description=" ",
     *    @Model(type=Page::class, groups={"Page:post", "post"})
     * ),
     * @SWG\Response(
     *   response=200, description=" ",
     *   @SWG\Schema(type="object", ref=@Model(type=Page::class, groups={"Page:get", "get", "File:min"})
     *   )
     * )
     * @SWG\Response(response=400, description="Invalid input")
     * @SWG\Response(response=404, description="Resource not found")
     * @SWG\Tag(name="ADMIN_Page")
     */
    public function update(Request $request, PageRepository $repository, $id)
    {
        /** @var \App\Entity\Page $entity */
        $entity = $repository->find($id);
        if(!$entity) {
            return $this->json_response(null, AppConsts::CODE_NOT_FOUND_404);
        }

        $form = $this->createForm(PageType::class, $entity);
        $form->submit($request->request->all());
        $form->handleRequest($request);

        $res = $this->validate($entity, ['Page:post']);
        if($res->type == AppConsts::SUCCESS) {

            $em = $this->em();
            $em->persist($entity);
            $em->flush();

            return $this->json_response(
                $entity,
                AppConsts::CODE_200,
                ["Page:get", "get", "File:min"]
            );
        }

        return $this->json_response($res, AppConsts::CODE_INVALID_INPUT_400);
    }

    /**
     * Delete
     * @Route("/delete/{id}", methods="DELETE")
     * @SWG\Response(response=204, description="Resource deleted")
     * @SWG\Response(response=404,description="Resource not found")
     * @SWG\Tag(name="ADMIN_Page")
     */
    public function delete(Request $request, PageRepository $repository, $id)
    {
        /** @var \App\Entity\Page $entity */
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
     * @SWG\Tag(name="ADMIN_Page")
     */
    public function isPublish(Request $request, PageRepository $repository, $id)
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

        return $this->json_response([], AppConsts::CODE_200, ['Page:get', 'get']);
    }
}
