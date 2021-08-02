<?php

namespace App\Controller\Pub;

use App\AppConsts;
use App\Controller\RestController;
use App\Entity\News;
use App\Entity\File;
use App\Repository\NewsRepository;
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
     * @SWG\Tag(name="PUB_News")
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
     * @Route("/{slug}", methods="GET")
     * @SWG\Response(
     *     response=200, description=" ",
     *     @SWG\Schema(type="object", ref=@Model(type=News::class, groups={"News:get", "get", "File:min"}) )
     * )
     * @SWG\Response(response=404, description="Not Found")
     * @SWG\Tag(name="PUB_News")
     */
    public function getAction(Request $request, NewsRepository $repository, $slug)
    {
        $entity = $repository->findOneBy(['slug' => $slug]);
        if(!$entity) {
            return $this->json_response(null, AppConsts::CODE_NOT_FOUND_404);
        }

        return $this->json_response(
            $entity,
            AppConsts::CODE_200,
            ["News:get", "get", "File:min"]
        );
    }

}
