<?php

namespace App\Controller\Admin;

use App\AppConsts;
use App\Controller\RestController;
use App\Entity\RequestForm;
use App\Repository\RequestFormRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @Route("/request_form")
 */
class RequestFormController extends RestController
{
    /**
     * List
     * @Route("/list", methods="GET")
     * @SWG\Parameter(name="page", in="query", type="integer", description="page num 1 2 3 4 ... ")
     * @SWG\Parameter(name="limit", in="query", type="integer", description="limit default 15 ")
     * @SWG\Response(
     *     response=200, description="",
     *     @SWG\Schema(type="array", @SWG\Items(ref=@Model(type=RequestForm::class, groups={"RequestForm:list", "get", "File:min" }))
     *    )
     * )
     * @SWG\Response(response=404, description="Not Found")
     * @SWG\Tag(name="ADMIN_RequestForm")
     */
    public function listAction(Request $request, RequestFormRepository $repository)
    {
        $qb = $repository->listAction();
        return $this->knpPaginationList(
            $qb,
            ["RequestForm:list", "RequestForm:get", "get", "File:min"],
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 15)
        );
    }

    /**
     * get
     * @Route("/{id}", methods="GET")
     * @SWG\Response(
     *     response=200, description=" ",
     *     @SWG\Schema(type="object", ref=@Model(type=RequestForm::class, groups={"RequestForm:get", "get", "File:min"}) )
     * )
     * @SWG\Response(response=404, description="Not Found")
     * @SWG\Tag(name="ADMIN_RequestForm")
     */
    public function getAction(Request $request, RequestFormRepository $repository, $id)
    {
        $entity = $repository->find($id);
        if(!$entity) {
            return $this->json_response(null, AppConsts::CODE_NOT_FOUND_404);
        }

        return $this->json_response(
            $entity,
            AppConsts::CODE_200,
            ["RequestForm:get", "get", ]
        );
    }



    /**
     * Update
     * @Route("/update_status/{id}", methods={"PUT"})
     * @SWG\Parameter(
     *     name="is_active", in="formData", type="string",
     *     description="is_active - true || false"
     * )
     * @SWG\Response(response=200, description="")
     * @SWG\Response(response=400, description="Invalid input")
     * @SWG\Response(response=404, description="Resource not found")
     * @SWG\Tag(name="ADMIN_RequestForm")
     */
    public function updateStatus(Request $request, RequestFormRepository $repository, $id)
    {
        $entity = $repository->find($id);
        if(!$entity) {
            return $this->json_response(null, AppConsts::CODE_NOT_FOUND_404);
        }

        $entity->setStatus(RequestForm::STATUS_SUCCESS);

        $em = $this->em();
        $em->persist($entity);
        $em->flush();

        return $this->json_response($entity, AppConsts::CODE_200, ["RequestForm:get"]);
    }

    /**
     * Delete
     * @Route("/delete/{id}", methods="DELETE")
     * @SWG\Response(response=204, description="Resource deleted")
     * @SWG\Response(response=404,description="Resource not found")
     * @SWG\Tag(name="ADMIN_RequestForm")
     */
    public function delete(Request $request, RequestFormRepository $repository, $id)
    {
        /** @var \App\Entity\RequestForm $entity */
        $entity = $repository->find($id);
        if(!$entity) {
            return $this->json_response(null, AppConsts::CODE_NOT_FOUND_404);
        }

        $em = $this->em();
        $em->remove($entity);
        $em->flush();

        return $this->json_response(null, AppConsts::CODE_DELETED_204);
    }

}
