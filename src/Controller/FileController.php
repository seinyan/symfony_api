<?php

namespace App\Controller;

use App\AppConsts;
use App\Entity\File;
use App\Form\AppFileType;
use App\Form\FileType;
use App\Form\User\RegisterType;
use App\Repository\FileRepository;;
use App\Services\FileService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @Route("/file")
 */
class FileController extends RestController
{
    /**
     * Upload file
     * @Route("/upload", methods={"POST"})
     * @SWG\Parameter(name="File", in="body",
     *   @Model(type=File::class, groups={"File:post"})
     * ),
     * @SWG\Response(response=200, description="",
     *   @SWG\Schema(
     *       type="object",
     *       ref=@Model(type=File::class, groups={"File:get"})
     *   )
     * )
     * @SWG\Response(response=400, description="Invalid input")
     * @SWG\Tag(name="File")
     */
    public function upload(Request $request, FileService $fileService)
    {
        $entity = new File();
        $form = $this->createForm(AppFileType::class, $entity);
        $form->submit($request->request->all());
        $form->handleRequest($request);
        $entity->setFile($request->files->get('file'));
        $entity->setType(File::TYPE_FILE);
        $res = $fileService->uploadFile($entity);

        return $this->json_response($res->data, $res->status, ['File:get']);
    }

    /**
     * Upload image
     * @Route("/image/upload", methods={"POST"})
     * @SWG\Parameter(name="File", in="body",
     *   @Model(type=File::class, groups={"File:post"})
     * ),
     * @SWG\Response(response=200, description="",
     *   @SWG\Schema(
     *       type="object",
     *       ref=@Model(type=File::class, groups={"File:get"})
     *   )
     * )
     * @SWG\Response(response=400, description="Invalid input")
     * @SWG\Tag(name="File")
     */
    public function uploadImage(Request $request, FileRepository $repository, FileService $fileService)
    {
        $entity = new File();

        if ($request->request->get('id')) {
            if ($request->request->get('id') !== 'null') {
                $entity = $repository->find($request->request->get('id'));
                if(!$entity) {
                    return $this->json_response(null, AppConsts::CODE_NOT_FOUND_404);
                }
            }
        }

        $form = $this->createForm(AppFileType::class, $entity);
        $form->submit($request->request->all());
        $form->handleRequest($request);
        $entity->setFile($request->files->get('file'));
        $entity->setType(File::TYPE_IMAGE);
        $res = $fileService->uploadImage($entity);

        return $this->json_response($res->data, $res->status, ['File:get']);
    }

    /**
     * Upload image
     * @Route("/image/upload_ckeditor", methods={"POST"})
     * @SWG\Parameter(name="File", in="body",
     *   @Model(type=File::class, groups={"File:post"})
     * ),
     * @SWG\Response(response=200, description="",
     *   @SWG\Schema(
     *       type="object",
     *       ref=@Model(type=File::class, groups={"File:get"})
     *   )
     * )
     * @SWG\Response(response=400, description="Invalid input")
     * @SWG\Tag(name="File")
     */
    public function uploadCkeditor(Request $request, FileService $fileService)
    {
        $entity = new File();
        $form = $this->createForm(AppFileType::class, $entity);
        $form->submit($request->request->all());
        $form->handleRequest($request);
        $entity->setFile($request->files->get('upload'));
        $entity->setType(File::TYPE_IMAGE);
        $entity->setSubPath('ckeditor');

        $res = $fileService->uploadImageCkeditor($entity);

        $uploaded = false;
        if($res->status === AppConsts::CODE_200) {
            $uploaded = true;
        }

        return $this->json_response([
            "uploaded" => $uploaded,
            "url" =>  $res->data->urlVp()
        ], 200, []);
//        return $this->json_response($res->data, $res->status, ['File:get']);
    }


    /**
     * Crop image
     * @Route("/crop/{id}", methods={"POST"})
     * @SWG\Parameter(
     *   name="File", in="body",
     *   @Model(type=File::class, groups={"File:post"})
     * ),
     * @SWG\Response(
     *   response=200, description="",
     *   @SWG\Schema(
     *       type="object",
     *       ref=@Model(type=File::class, groups={"File:get"})
     *   )
     * )
     * @SWG\Response(response=400, description="Invalid input")
     * @SWG\Tag(name="File")
     */
    public function imageCrop(Request $request, FileService $fileService, FileRepository $repository, $id)
    {
        $entity = $repository->find($id);
        if (!$entity) {
            return $this->json_response(null, AppConsts::CODE_NOT_FOUND_404);
        }

        $form = $this->createForm(AppFileType::class, $entity);
        $form->submit($request->request->all());
        $form->handleRequest($request);

        if ($entity->width === null && $entity->height === null && $entity->x === null && $entity->y === null) {
            return $this->json_response("INVALID_INPUT", AppConsts::CODE_INVALID_INPUT_400);
        }

        $res = $fileService->imageCrop($entity);

        return $this->json_response($res->data, $res->status, ['File:get']);
    }


    /**
     * get
     * @Route("/get/{id}", methods="GET")
     * @SWG\Response(response=200, description=" ")
     * @SWG\Response(response=404, description="Not Found")
     * @SWG\Tag(name="File")
     */
    public function getPrivateFile(Request $request, FileRepository $repository, $id)
    {
        $entity = $repository->find($id);
        if(!$entity) {
            return new JsonResponse(null,AppConsts::CODE_NOT_FOUND_404);
        }

        $path = $this->getParameter('kernel.project_dir').'/'.$entity->get_full_path();
        return new BinaryFileResponse($path);
    }

    /**
     * Delete
     * @Route("/delete/{id}", methods="DELETE")
     * @SWG\Response(response=204, description="Resource deleted")
     * @SWG\Response(response=404,description="Resource not found")
     * @SWG\Tag(name="File")
     */
    public function delete(Request $request, FileRepository $repository, $id)
    {
        /** @var \App\Entity\Image $entity */
        $entity = $repository->find($id);
        if(!$entity) {
            return $this->json_response(null, AppConsts::CODE_NOT_FOUND_404);
        }

        $entity->removeFile();

        $em = $this->em();
        $em->remove($entity);
        $em->flush();

        return $this->json_response(null, AppConsts::CODE_DELETED_204);
    }

}
