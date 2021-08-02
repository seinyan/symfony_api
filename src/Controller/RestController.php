<?php

namespace App\Controller;

use App\AppConsts;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Doctrine\ORM\EntityManager;

/**
 * Class RestController
 * @package App\Controller
 */
class RestController extends AbstractController
{
    /** @var ValidatorInterface  */
    public $validator;

    /** @var PaginatorInterface  */
    public $paginator;

    /** @var Serializer  */
    public $JMSserializer;

    /** @var SerializationContext */
    public $JMSContext;

    /**
     * BaseRestController constructor.
     * @param PaginatorInterface $paginator
     * @param ValidatorInterface $validator
     */
    public function __construct(PaginatorInterface $paginator, ValidatorInterface $validator)
    {
        $this->paginator = $paginator;
        $this->validator = $validator;

        $this->JMSserializer =
            SerializerBuilder::create()
                //->setCacheDir('jms_serializer')
                ->setDebug(false)
                ->build();

        $this->JMSContext = new SerializationContext();
        $this->JMSContext->setSerializeNull(true);
    }

    /**
     * @param $qb
     * @param array|null $groups
     * @param int $page
     * @param int $limit
     * @return JsonResponse
     */
    public function knpPaginationList($qb,array $groups=null, $page=1, $limit=15)
    {
        $pagination = $this->paginator->paginate($qb->getQuery(), $page, $limit);
        $meta = $pagination->getPaginationData();
        $res = [
            "current" => $meta["current"],
            "numItemsPerPage" => $meta['numItemsPerPage'],
            "pageCount" =>  $meta['pageCount'],
            "totalCount" =>  $meta['totalCount'],
            "pageRange" =>  $meta['pageRange'],
            "currentItemCount" =>  $meta['currentItemCount'],
            'items' => $pagination->getItems()
        ];

        return $this->json_response($res, AppConsts::CODE_200, $groups);
    }

    /*******************************/
    /*******   functions  **********/
    /*******************************/

    /**
     * @param $data
     * @param $code
     * @param array $groups
     * @return JsonResponse
     */
    public function json_response($data, $code, array $groups=[])
    {
        $json = $this->to_json($data, $groups);
        return new JsonResponse(
            $json,
            $code,
            [],
            true
        );
    }

    /**
     * @param $data
     * @param array $groups
     * @return mixed|string
     */
    public function to_json($data, array $groups=[])
    {
        if($groups) {
            $this->JMSContext->setGroups($groups);
        }

        return $this->JMSserializer->serialize($data, 'json', $this->JMSContext);
    }

    /**
     * @param $entity
     * @param array|null $groups
     * @return \stdClass
     */
    public function validate($entity, array $groups=null)
    {
        $res = new \stdClass;
        $res->type       = null;
        $res->title      = null;
        $res->violations = [];

        $errors = $this->validator->validate($entity, null, $groups);

        if (count($errors) > 0) {
            $res->type  = AppConsts::ERROR;
            $res->title = AppConsts::ERROR;
            $res->violations = $this->transformErr($errors);
        }else {
            $res->type  = AppConsts::SUCCESS;
            $res->title = AppConsts::SUCCESS;
        }

        return $res;
    }

    /**
     * @param $errors
     * @return array
     */
    public function transformErr($errors)
    {
        $violations = [];
        foreach ($errors as $err) {
            $violation = [
                "propertyPath" => $err->getPropertyPath(),
                "title" => $err->getMessage(),
            ];
            $violations[] = $violation;
        }

        return $violations;
    }


    /*******************************/
    /*****  EntityManager  *********/
    /*******************************/

    /**
     * @param null $managerName
     * @return \Doctrine\Persistence\ObjectManager
     */
    public function em($managerName=null)
    {
        return $this->getDoctrine()->getManager($managerName);
    }

    /**
     * @param $model
     * @param null $managerName
     * @return \Doctrine\Persistence\ObjectRepository
     */
    public function Repository($model, $managerName=null)
    {
        return $this->getDoctrine()->getRepository($model, $managerName);
    }

}
