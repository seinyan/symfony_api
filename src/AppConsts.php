<?php
namespace App;



/**
 * Class AppConsts
 * @package App
 */
class AppConsts
{
    const POST   = 'POST';
    const GET    = 'GET';
    const PUT    = 'PUT';
    const DELETE = 'DELETE';


    const FILE_TYPE_IMAGE = 'image';
    const FILE_TYPE_AUDIO = 'audio';
    const FILE_TYPE_FILE  = 'file';
    const FILE_TYPE_IMAGE_CROP  = 'image_crop';

    const ERROR   = "error";
    const SUCCESS = "success";

    const CODE_200 = 200;
    const CODE_CREATED_201 = 201;
    const CODE_DELETED_204 = 204;
    const CODE_INVALID_INPUT_400 = 400;
    const CODE_NOT_FOUND_404 = 404;
    const CODE_500 = 500;
    const CODE_FORBIDDEN_403 = 403;
    const CODE_SEE_OTHER_303 = 303;

    /**
     * collection LIST GET  200
     */

    /**
     * GET /object/{id}
     * 200 Book resource response
     * 404 Resource not found
     */

    /**
     * Create POST
     * 201  Book resource created
     * 400 Invalid input
     * 404 Resource not found
     */

    /**
     * DELETE /books/{id}
     * 204 Book resource deleted
     * 404 Resource not found
     */

    /**
     * Replaces the Object resource.
     * PUT /books/{id}
     * 200 Book resource updated
     * 400 Invalid input
     * 404 Resource not found
     */

    /**
     * Updates the Object resource.
     * PATCH /books/{id} Updates the object resource.
     * 200 Book resource updated
     * 400 Invalid input
     * 404 Resource not found
     */

}