<?php

namespace AppBundle\Controller\IOT;

use AppBundle\Entity\IOT\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Form\Type\IOT\DeviceType;
use AppBundle\Entity\IOT\Device;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;

class DeviceController extends Controller
{
    /**
     * @Rest\View(serializerGroups={"device"})
     * @Rest\Put("/devices/{id}")
     */
    public function updateDeviceAction(Request $request)
    {
        return $this->updateDevice($request, true);
    }

    /**
     * @Rest\View(serializerGroups={"device"})
     * @Rest\Patch("/devices/{id}")
     */
    public function patchDeviceAction(Request $request)
    {
        return $this->updateDevice($request, false);
    }

    private function updateDevice(Request $request, $clearMissing)
    {
        $device = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:IOT\Device')
            ->find($request->get('id')); // L'identifiant en tant que paramètre n'est plus nécessaire
        /* @var $device Device */

        if (empty($device)) {
            return \FOS\RestBundle\View\View::create(['message' => 'Device not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(DeviceType::class, $device);

        // Le paramètre false dit à Symfony de garder les valeurs dans notre
        // entité si l'utilisateur n'en fournit pas une dans sa requête
        $form->submit($request->request->all(), $clearMissing);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($device);
            $em->flush();
            return $device;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT,serializerGroups={"device"})
     * @Rest\Delete("/devices/{id}")
     */
    public function removeDeviceAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $device = $em->getRepository('AppBundle:IOT\Device')
            ->find($request->get('id'));
        /* @var $device Device */

        if (!$device) {
            return \FOS\RestBundle\View\View::create(['message' => 'Device not found'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($device);
        $em->flush();
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED,serializerGroups={"device"})
     * @Rest\Post("/devices")
     */
    public function postDevicesAction(Request $request)
    {
        $customer = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Customer')
            ->find($request->get('id'));
        /* @var $customer Customer */

        if (empty($customer)) {
            return $this->customerNotFound();
        }
        $device = new Device();
        $device->setCustomer($customer); // Ici, le lieu est associé au prix
        $form = $this->createForm(DeviceType::class, $device);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($device);
            $em->flush();
            return $device;
        } else {
            return $form;
        }
    }

    private function customerNotFound()
    {
        return \FOS\RestBundle\View\View::create(['message' => 'Customer not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * @Rest\View(serializerGroups={"device"})
     * @Rest\Get("/devices")
     * @QueryParam(name="offset", requirements="\d+", default="", description="Index de début de la pagination")
     * @QueryParam(name="limit", requirements="\d+", default="", description="Nombre d'éléments à afficher")
     * @QueryParam(name="sort", requirements="(asc|desc)", nullable=true, description="Ordre de tri (basé sur le nom)")
     */
    public function getDevicesAction(Request $request, ParamFetcher $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $sort = $paramFetcher->get('sort');

        $qb = $this->get('doctrine.orm.entity_manager')->createQueryBuilder();
        $qb->select('p')
            ->from('AppBundle:IOT\Device', 'p');

        if ($offset != "") {
            $qb->setFirstResult($offset);
        }

        if ($limit != "") {
            $qb->setMaxResults($limit);
        }

        if (in_array($sort, ['asc', 'desc'])) {
            $qb->orderBy('p.name', $sort);
        }
        $device = $qb->getQuery()->getResult();

        return $device;
    }

    /**
     * @Rest\View(serializerGroups={"device"})
     * @Rest\Get("/devices/{id}")
     */
    public function getDeviceAction(Request $request)
    {
        $device = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:IOT\Device')
            ->find($request->get('id')); // L'identifiant en tant que paramétre n'est plus nécessaire
        /* @var $device Device */

        if (empty($device)) {
            return \FOS\RestBundle\View\View::create(['message' => 'Device not found'], Response::HTTP_NOT_FOUND);
        }

        return $device;
    }
}



