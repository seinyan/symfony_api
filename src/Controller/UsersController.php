<?php

namespace App\Controller;

use App\AppConsts;
use App\Entity\Notification;
use App\Entity\UserLog;
use App\Entity\UserNotification;
use App\Entity\UserToken;
use App\Form\User\RegisterType;
use App\Form\User\UpdateType;
use App\Repository\UserLogRepository;
use App\Repository\UserNotificationRepository;
use App\Repository\UserRepository;
use App\Repository\UserTokenRepository;
use App\Services\FileService;
use App\Services\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use App\Entity\User;

/**
 * @Route("/currentuser")
 */
class UsersController extends RestController
{
    /**
     * Get Current User
     * @Route(path="/get", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="User",
     *     @SWG\Schema(ref=@Model(type=User::class, groups={"user:get", "File:min"})),
     * )
     * @SWG\Tag(name="CurrentUser")
     */
    public function getCurrentUser(Request $request)
    {
        return $this->json_response($this->getUser(), AppConsts::CODE_200, ['User:get', 'File:min']);
    }

    /**
     * Update Current User
     * @Route("/update", methods={"PUT"})
     * @SWG\Parameter(
     *    name="Executor", in="body",
     *    @Model(type=User::class, groups={"user:post"})
     * ),
     * @SWG\Response(
     *   response=200, description="",
     *   @SWG\Schema(
     *   type="object", ref=@Model(type=User::class, groups={"user:get", "File:min"}) )
     * )
     * @SWG\Response(response=400, description="Invalid input")
     * @SWG\Response(response=404, description="Resource not found")
     * @SWG\Tag(name="CurrentUser")
     */
    public function updateCurrentUser(Request $request, UserService $userService)
    {
        /** @var \App\Entity\User $user */
        $entity = $this->getUser();

        $form = $this->createForm(UpdateType::class, $entity);
        $form->submit($request->request->all());
        $form->handleRequest($request);

        $res = $this->validate($entity, ['User:post']);
        if($res->type == AppConsts::SUCCESS) {

            $userService->update($entity);

            return $this->json_response($entity, AppConsts::CODE_200, ['User:get', 'File:min']);
        }

        return $this->json_response($res, AppConsts::CODE_INVALID_INPUT_400);
    }

    /**
     * Update Current User
     * @Route("/update_pass", methods={"PUT"})
     * @SWG\Parameter(
     *    name="Executor", in="body",
     *    @Model(type=User::class, groups={"user:post"})
     * ),
     * @SWG\Response(
     *   response=200, description="",
     *   @SWG\Schema(
     *   type="object",
     *      ref=@Model(type=User::class, groups={"user:get", "File:min"})
     *   )
     * )
     * @SWG\Response(response=400, description="Invalid input")
     * @SWG\Response(response=404, description="Resource not found")
     * @SWG\Tag(name="CurrentUser")
     */
    public function updateCurrentUserPass(Request $request, UserService $userService)
    {
        if (!$request->request->get('password')) {
            return $this->json_response(null, AppConsts::CODE_INVALID_INPUT_400);
        }

        $userService->updatePassword($this->getUser(), $request->request->get('password'));

        return $this->json_response(null, AppConsts::CODE_200, []);
    }


    /**
     * List
     * @Route("/notifications", methods="GET")
     * @SWG\Response(
     *     response=200, description="",
     *     @SWG\Schema(type="array", @SWG\Items(ref=@Model(type=UserNotification::class, groups={"UserNotification:list", "get", "File:min" }))
     *    )
     * )
     * @SWG\Response(response=404, description="Not Found")
     * @SWG\Tag(name="CurrentUser")
     */
    public function getNotifications(Request $request, UserNotificationRepository $repository)
    {
        return $this->json_response(
            $repository->getItems($this->getUser()),
            AppConsts::CODE_200,
            ["Notification:get", "get"]
        );
    }

    /**
     * Update
     * @Route("/notifications/read/{id}", methods={"POST"})
     * @SWG\Response(response=200, description="")
     * @SWG\Response(response=400, description="Invalid input")
     * @SWG\Response(response=404, description="Resource not found")
     * @SWG\Tag(name="CurrentUser")
     */
    public function readNotifications(Request $request, UserNotificationRepository $repository, $id)
    {
        $entity = $repository->findOneBy([
            'id' => $id,
            'user' => $this->getUser()->getId()
        ]);
        if(!$entity) {
            return $this->json_response(null, AppConsts::CODE_NOT_FOUND_404);
        }

        $entity->setIsRead(true);

        $em = $this->em();
        $em->persist($entity);
        $em->flush();

        return $this->json_response([], AppConsts::CODE_200, []);
    }


    /**
     * List
     * @Route("/logs", methods="GET")
     * @SWG\Parameter(name="page", in="query", type="integer", description="page 1 2 3..")
     * @SWG\Parameter(name="limit", in="query", type="integer", description="default 15")
     * @SWG\Response(response=200, description="")
     * @SWG\Response(response=404, description="Not Found")
     * @SWG\Tag(name="CurrentUser")
     */
    public function logs(Request $request, UserLogRepository $repository)
    {
        $qb = $repository->listAction($this->getUser());
        return $this->knpPaginationList(
            $qb,
            ['UserLog:get', 'UserAgentData:get'],
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 15)
        );
    }

    /**
     * List
     * @Route("/sessions", methods="GET")
     * @SWG\Parameter(name="page", in="query", type="integer", description="page 1 2 3..")
     * @SWG\Parameter(name="limit", in="query", type="integer", description="default 15")
     * @SWG\Response(response=200, description="")
     * @SWG\Response(response=404, description="Not Found")
     * @SWG\Tag(name="CurrentUser")
     */
    public function sessions(Request $request, UserTokenRepository $repository)
    {
        return $this->json_response(
            $repository->getItems($this->getUser()),
            AppConsts::CODE_200,
            ["UserToken:get", 'UserAgentData:get']
        );
    }

    /**
     * Clear all session
     * @Route("/sessions/clear_all", methods={"POST"})
     * @SWG\Response(response=200, description="")
     * @SWG\Tag(name="CurrentUser")
     */
    public function clearAllSession(Request $request, UserService $userService)
    {
        $userService->clearAllSession($this->getUser());
        return $this->json_response(null, AppConsts::CODE_200);
    }
}
