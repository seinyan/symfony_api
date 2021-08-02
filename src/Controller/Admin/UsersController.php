<?php

namespace App\Controller\Admin;

use App\AppConsts;
use App\Controller\RestController;
use App\Entity\File;
use App\Form\User\RegisterType;
use App\Form\User\UpdateType;
use App\Form\User\UserAdminType;
use App\Form\User\UserType;
use App\Repository\UserRepository;
use App\Services\FileService;
use App\Services\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/user")
 */
class UsersController extends RestController
{
    /**
     * List user
     * @Route("/list", methods="GET")
     * @SWG\Parameter(name="page", in="query", type="integer", description="page 1 2 3..")
     * @SWG\Parameter(name="limit", in="query", type="integer", description="default 15")
     * @SWG\Response(response=200, description="",
     *   @SWG\Schema(
     *     type="array",
     *     @SWG\Items(ref=@Model(type=User::class,  groups={"user:list", "File:min"} ))
     *   )
     * )
     * @SWG\Response(response=404, description="Not Found")
     * @SWG\Tag(name="ADMIN_User")
     */
    public function listAction(Request $request, UserRepository $repository)
    {
        $qb = $repository->listAction($request);
        return $this->knpPaginationList(
            $qb,
            ['User:list', 'File:min'],
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 15)
        );
    }

    /**
     * get
     * @Route("/{id}", methods="GET")
     * @SWG\Response(response=200, description=" ",
     *   @SWG\Schema(
     *      type="object",
     *      ref=@Model(type=User::class, groups={"user:get", "File:min"})
     *    )
     * )
     * @SWG\Response(response=404, description="Not Found")
     * @SWG\Tag(name="ADMIN_User")
     */
    public function getAction(Request $request, UserRepository $repository, $id)
    {
        $entity = $repository->find($id);
        if(!$entity) {
            return $this->json_response(null, AppConsts::CODE_NOT_FOUND_404);
        }

        return $this->json_response($entity, AppConsts::CODE_200, ['User:list', 'File:min']);
    }

    /**
     * create
     * @Route("/create", methods={"POST"})
     * @SWG\Parameter(
     *   name="create", in="body",
     *   @Model(type=User::class, groups={"user:post"})
     * ),
     * @SWG\Response(
     *   response=201, description="",
     *   @SWG\Schema(
     *   type="object",
     *       ref=@Model(type=User::class, groups={"user:get", "File:min"})
     *   )
     * )
     * @SWG\Response(response=400, description="Invalid input")
     * @SWG\Tag(name="ADMIN_User")
     */
    public function create(Request $request, UserService $userService)
    {
        $entity = new User();
        $form = $this->createForm(UserAdminType::class, $entity);
        $form->submit($request->request->all());
        $form->handleRequest($request);

        $res = $this->validate($entity, ["User:register"]);
        if($res->type == AppConsts::SUCCESS) {
            $em = $this->em();

            $entity = $userService->registerByAdmin($entity);

            $em->persist($entity);
            $em->flush();

            return $this->json_response($entity, AppConsts::CODE_CREATED_201, ['User:list', 'File:min']);
        }

        return $this->json_response($res, AppConsts::CODE_INVALID_INPUT_400);
    }

    /**
     * Update
     * @Route("/update/{id}", methods={"PUT"})
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
     * @SWG\Tag(name="ADMIN_User")
     */
    public function update(Request $request, UserRepository $repository, UserService $userService, $id)
    {
        /** @var \App\Entity\User $entity */
        $entity = $repository->find($id);
        if(!$entity) {
            return $this->json_response(null, AppConsts::CODE_NOT_FOUND_404);
        }

        $form = $this->createForm(UpdateType::class, $entity);
        $form->submit($request->request->all());
        $form->handleRequest($request);

        $res = $this->validate($entity, ["User:register"]);
        if($res->type == AppConsts::SUCCESS) {

            $em = $this->em();
            $em->persist($entity);
            $em->flush();

            return $this->json_response($entity, AppConsts::CODE_200, ['User:list', 'File:min']);
        }

        return $this->json_response($res, AppConsts::CODE_INVALID_INPUT_400);
    }

    /**
     * Update Current User
     * @Route("/update_pass/{id}", methods={"PUT"})
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
     * @SWG\Tag(name="ADMIN_User")
     */
    public function updateCurrentUserPass(Request $request, UserRepository $repository, UserService $userService, $id)
    {
        $entity = $repository->find($id);
        if(!$entity) {
            return $this->json_response(null, AppConsts::CODE_NOT_FOUND_404);
        }

        if (!$request->request->get('password')) {
            return $this->json_response(null, AppConsts::CODE_INVALID_INPUT_400);
        }

        $userService->updatePassword($entity, $request->request->get('password'));

        return $this->json_response(null, AppConsts::CODE_200, []);
    }

    /**
     * Update
     * @Route("/is_active/{id}", methods={"PUT"})
     * @SWG\Parameter(
     *     name="is_active", in="formData", type="string",
     *     description="is_active - true || false"
     * )
     * @SWG\Response(response=200, description="")
     * @SWG\Response(response=400, description="Invalid input")
     * @SWG\Response(response=404, description="Resource not found")
     * @SWG\Tag(name="ADMIN_User")
     */
    public function isActive(Request $request, UserRepository $repository, $id)
    {
        $entity = $repository->find($id);
        if(!$entity) {
            return $this->json_response(null, AppConsts::CODE_NOT_FOUND_404);
        }

        $is_active = $request->request->getBoolean('is_active');
        $entity->setIsActive($is_active);

        $em = $this->em();
        $em->persist($entity);
        $em->flush();

        return $this->json_response([], AppConsts::CODE_200, ['User:list', 'File:min']);
    }

}
