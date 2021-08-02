<?php

namespace App\Controller\Admin;

use App\Controller\RestController;
use App\Entity\Subscriber;
use App\Repository\SubscriberRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @Route("/subscriber")
 */
class SubscriberController extends RestController
{
    /**
     * List
     * @Route("/list", methods="GET")
     * @SWG\Parameter(name="page", in="query", type="integer", description="page num 1 2 3 4 ... ")
     * @SWG\Parameter(name="limit", in="query", type="integer", description="limit default 15 ")
     * @SWG\Response(
     *     response=200, description="",
     *     @SWG\Schema(type="array", @SWG\Items(ref=@Model(type=Subscriber::class, groups={"Subscriber:list", "get", "File:min" }))
     *    )
     * )
     * @SWG\Response(response=404, description="Not Found")
     * @SWG\Tag(name="ADMIN_Subscriber")
     */
    public function listAction(Request $request, SubscriberRepository $repository)
    {
        $qb = $repository->listAction();
        return $this->knpPaginationList(
            $qb,
            ["Subscriber:list","get"],
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 15)
        );
    }

}
