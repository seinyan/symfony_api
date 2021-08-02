<?php

namespace App\Controller\Admin;

use App\AppConsts;
use App\Controller\RestController;
use App\Entity\News;
use App\Entity\File;
use App\Form\NewsType;
use App\Repository\NewsRepository;
use App\Services\SlugService;
use App\Utils\Slug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @Route("/news")
 */
class NewsController extends RestController
{
    /**
     * List
     * @Route("/list", methods="GET")
     * @SWG\Parameter(name="page", in="query", type="integer", description="page num 1 2 3 4 ... ")
     * @SWG\Parameter(name="limit", in="query", type="integer", description="limit default 15 ")
     * @SWG\Response(
     *     response=200, description="",
     *     @SWG\Schema(type="array", @SWG\Items(ref=@Model(type=News::class, groups={"News:list", "get", "File:min" }))
     *    )
     * )
     * @SWG\Response(response=404, description="Not Found")
     * @SWG\Tag(name="ADMIN_News")
     */
    public function listAction(Request $request, NewsRepository $repository)
    {
        $qb = $repository->listAction();
        return $this->knpPaginationList(
            $qb,
            ["News:list", "News:get", "get", "File:min"],
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 15)
        );
    }

    /**
     * get
     * @Route("/{id}", methods="GET")
     * @SWG\Response(
     *     response=200, description=" ",
     *     @SWG\Schema(type="object", ref=@Model(type=News::class, groups={"News:get", "get", "File:min"}) )
     * )
     * @SWG\Response(response=404, description="Not Found")
     * @SWG\Tag(name="ADMIN_News")
     */
    public function getAction(Request $request, NewsRepository $repository, $id)
    {
        $entity = $repository->find($id);
        if(!$entity) {
            return $this->json_response(null, AppConsts::CODE_NOT_FOUND_404);
        }

        return $this->json_response(
            $entity,
            AppConsts::CODE_200,
            ["News:list", "News:get", "get", "File:min"]
        );
    }

    /**
     * create
     * @Route("/create", methods={"POST"})
     * @SWG\Parameter(
     *   name="create", in="body", description="  ",
     *   @Model(type=News::class, groups={"News:post", "post"})
     * ),
     * @SWG\Response(
     *   response=201, description="Created",
     *   @SWG\Schema(type="object", ref=@Model(type=News::class, groups={"News:get", "get", "File:min"}) )
     * )
     * @SWG\Response(response=400, description="Invalid input")
     * @SWG\Tag(name="ADMIN_News")
     */
    public function create(Request $request, NewsRepository $contactRepository, Slug $slug)
    {
        $entity = new News();
        $form = $this->createForm(NewsType::class, $entity);
        $form->submit($request->request->all());
        $form->handleRequest($request);

        $res = $this->validate($entity, ['News:post']);
        if($res->type == AppConsts::SUCCESS) {
            $em = $this->em();
            $em->persist($entity);
            $em->flush();

            $entity->setCreatedAt(new \DateTime());
            $entity->setSlug($slug->textToSlug($entity->getTitle()).$entity->getId());

            $em->flush();

            return $this->json_response(
                $entity,
                AppConsts::CODE_CREATED_201,
                ["News:list", "News:get", "get", "File:min"]
            );
        }

        return $this->json_response($res, AppConsts::CODE_INVALID_INPUT_400);
    }

    /**
     * Update
     * @Route("/update/{id}", methods={"PUT"})
     * @SWG\Parameter(
     *    name="Campaign", in="body", description=" ",
     *    @Model(type=News::class, groups={"News:post", "post"})
     * ),
     * @SWG\Response(
     *   response=200, description=" ",
     *   @SWG\Schema(type="object", ref=@Model(type=News::class, groups={"News:get", "get", "File:min"})
     *   )
     * )
     * @SWG\Response(response=400, description="Invalid input")
     * @SWG\Response(response=404, description="Resource not found")
     * @SWG\Tag(name="ADMIN_News")
     */
    public function update(Request $request, NewsRepository $repository, $id)
    {
        /** @var \App\Entity\News $entity */
        $entity = $repository->find($id);
        if(!$entity) {
            return $this->json_response(null, AppConsts::CODE_NOT_FOUND_404);
        }

        $form = $this->createForm(NewsType::class, $entity);
        $form->submit($request->request->all());
        $form->handleRequest($request);

        $res = $this->validate($entity, ['News:post']);
        if($res->type == AppConsts::SUCCESS) {

            $entity->setUpdatedAt(new \DateTime());

            $em = $this->em();
            $em->persist($entity);
            $em->flush();

            return $this->json_response(
                $entity,
                AppConsts::CODE_200,
                ["News:list", "News:get", "get", "File:min"]
            );
        }

        return $this->json_response($res, AppConsts::CODE_INVALID_INPUT_400);
    }

    /**
     * Delete
     * @Route("/delete/{id}", methods="DELETE")
     * @SWG\Response(response=204, description="Resource deleted")
     * @SWG\Response(response=404,description="Resource not found")
     * @SWG\Tag(name="ADMIN_News")
     */
    public function delete(Request $request, NewsRepository $repository, $id)
    {
        /** @var \App\Entity\News $entity */
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
     * @SWG\Tag(name="ADMIN_News")
     */
    public function isPublish(Request $request, NewsRepository $repository, $id)
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

        return $this->json_response([], AppConsts::CODE_200, ['News:get', 'get']);
    }
}
