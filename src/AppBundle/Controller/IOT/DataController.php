<?php

namespace AppBundle\Controller\IOT;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Form\Type\IOT\DataType;
use AppBundle\Entity\IOT\Data;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;

class DataController extends Controller
{
    /**
     * @Rest\View(serializerGroups={"data"})
     * @Rest\Put("/datas/{id}")
     */
    public function updateDataAction(Request $request)
    {
        return $this->updateData($request, true);
    }

    /**
     * @Rest\View(serializerGroups={"data"})
     * @Rest\Patch("/datas/{id}")
     */
    public function patchDataAction(Request $request)
    {
        return $this->updateData($request, false);
    }

    private function updateData(Request $request, $clearMissing)
    {
        $data = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:IOT\Data')
            ->find($request->get('id')); // L'identifiant en tant que paramètre n'est plus nécessaire
        /* @var $data Data */

        if (empty($data)) {
            return \FOS\RestBundle\View\View::create(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(DataType::class, $data);

        // Le paramètre false dit à Symfony de garder les valeurs dans notre
        // entité si l'utilisateur n'en fournit pas une dans sa requête
        $form->submit($request->request->all(), $clearMissing);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($data);
            $em->flush();
            return $data;
        } else {
            return $form;
        }
    }
    //TODO: Cascading gesture
    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT,serializerGroups={"data"})
     * @Rest\Delete("/datas/{id}")
     */
    public function removeDataAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $data = $em->getRepository('AppBundle:IOT\Data')
            ->find($request->get('id'));
        /* @var $data Data */

        if (!$data) {
            return;
        }

        $em->remove($data);
        $em->flush();
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED,serializerGroups={"data"})
     * @Rest\Post("/datas")
     */
    public function postDatasAction(Request $request)
    {
        $data = new Data();
        $form = $this->createForm(DataType::class, $data);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($data);
            $em->flush();
            return $data;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerGroups={"data"})
     * @Rest\Get("/datas")
     * @QueryParam(name="offset", requirements="\d+", default="", description="Index de début de la pagination")
     * @QueryParam(name="limit", requirements="\d+", default="", description="Nombre d'éléments à afficher")
     * @QueryParam(name="sort", requirements="(asc|desc)", nullable=true, description="Ordre de tri (basé sur le nom)")
     */
    public function getDatasAction(Request $request, ParamFetcher $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $sort = $paramFetcher->get('sort');

        $qb = $this->get('doctrine.orm.entity_manager')->createQueryBuilder();
        $qb->select('p')
            ->from('AppBundle:IOT\Data', 'p');

        if ($offset != "") {
            $qb->setFirstResult($offset);
        }

        if ($limit != "") {
            $qb->setMaxResults($limit);
        }

        if (in_array($sort, ['asc', 'desc'])) {
            $qb->orderBy('p.name', $sort);
        }
        $data = $qb->getQuery()->getResult();

        return $data;
    }

    /**
     * @Rest\View(serializerGroups={"data"})
     * @Rest\Get("/datas/{id}")
     */
    public function getDataAction(Request $request)
    {
        $data = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:IOT\Data')
            ->find($request->get('id')); // L'identifiant en tant que paramétre n'est plus nécessaire
        /* @var $data Data */

        if (empty($data)) {
            return \FOS\RestBundle\View\View::create(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
        }

        return $data;
    }
}



