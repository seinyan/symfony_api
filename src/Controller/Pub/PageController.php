<?php

namespace App\Controller\Pub;

use App\AppConsts;
use App\Controller\RestController;
use App\Entity\Page;
use App\Repository\PageRepository;
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
     * @SWG\Tag(name="PUB_Page")
     */
    public function listAction(Request $request, PageRepository $repository)
    {
        $groups = ["Page:list", "Page:get", "get", "File:min"];

        if ($request->query->get('group') === 'min') {
            $groups = ["Page:min"];
        }

        return $this->json_response(
            $repository->listPub(),
            AppConsts::CODE_200,
            $groups
        );
    }

    /**
     * get
     * @Route("/{slug}", methods="GET")
     * @SWG\Response(
     *     response=200, description=" ",
     *     @SWG\Schema(type="object", ref=@Model(type=Page::class, groups={"Page:get", "get", "File:min"}) )
     * )
     * @SWG\Response(response=404, description="Not Found")
     * @SWG\Tag(name="PUB_Page")
     */
    public function getAction(Request $request, PageRepository $repository, $slug)
    {
        $entity = $repository->findOneBy([
            'slug' => $slug,
            'isPublish' => true,
        ]);
        if(!$entity) {
            return $this->json_response(null, AppConsts::CODE_NOT_FOUND_404);
        }

        return $this->json_response(
            $entity,
            AppConsts::CODE_200,
            ["Page:get", "get", "File:min"]
        );
    }

}
