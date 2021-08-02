<?php

namespace App\Controller;

use App\AppConsts;
use App\Entity\User;
use App\Form\User\RegisterType;
use App\Services\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * Class SecurityController
 * @package App\Controller
 */ //  * @Route(path="/api")
class SecurityController extends RestController
{
    /**
     * login
     * @Route("/login", name="api_login" , methods={"POST"})
     * @SWG\Parameter(name="username", in="formData", type="string")
     * @SWG\Parameter(name="password", in="formData", type="string")
     * @SWG\Response(
     *     response=200, description="User logined",
     *     @SWG\Schema(ref=@Model(type=User::class, groups={"token"})),
     * )
     * @SWG\Response(response=401, description="code 401")
     * @SWG\Tag(name="Security")
     */
    public function login(AuthenticationUtils $authenticationUtils) { }

    /**
     * logout
     * @Route("/logout", name="api_logout", methods={"POST"})
     * @SWG\Response(response=200, description="logout")
     * @SWG\Tag(name="Security")
     */
    public function logout(Request $request, UserService $userService)
    {
        $userService->logout($this->getUser(), $request);

        return $this->json_response(null, AppConsts::CODE_200);
    }

    /**
     * register
     * @Route(path="/register", methods={"POST"})
     * @SWG\Parameter(
     *   name="user", in="body", description="register user",
     *   @Model(type=User::class, groups={"user:register"})
     * )
     * @SWG\Response(
     *   response=200, description="User registered",
     *   @SWG\Schema(ref=@Model(type=User::class, groups={"user:get"})),
     * )
     * @SWG\Response(response=400, description="Invalid input")
     * @SWG\Tag(name="Security")
     */
    public function register(Request $request, UserService $userService)
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->submit($request->request->all());
        $form->handleRequest($request);

        $res = $this->validate($user, ["User:register"]);
        if($res->type == AppConsts::SUCCESS) {
            $userService->register($user);
            return $this->json_response(null, AppConsts::CODE_200, ['User:get']);
        }

        return $this->json_response($res, AppConsts::CODE_INVALID_INPUT_400);
    }

    /**
     * restore
     * @Route(path="/restore", methods={"POST"})
     * @SWG\Parameter(
     *     name="user", in="body", description="restore Send new pass",
     *     @Model(type=User::class, groups={"user:restore"})
     * ),
     * @SWG\Response(response=200, description="restore")
     * @SWG\Response(response=400, description="Invalid input")
     * @SWG\Tag(name="Security")
     */
    public function restore(Request $request, UserService $userService)
    {
        /** @var \App\Entity\User $user */
        $user = $this->Repository(User::class)->loadUserByUsername($request->request->get('email'));
        if(!$user) {
            return $this->json_response("NOT_FOUND", AppConsts::CODE_INVALID_INPUT_400);
        }

        $userService->restore($user);

        return $this->json_response(null, AppConsts::CODE_200);
    }

}
