<?php

namespace AppBundle\Controller\IOT;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Form\Type\IOT\CustomerType;
use AppBundle\Entity\IOT\Customer;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;

class CustomerController extends Controller
{
    /**
     * @Rest\View(serializerGroups={"customer"})
     * @Rest\Put("/customers/{id}")
     */
    public function updateCustomerAction(Request $request)
    {
        return $this->updateCustomer($request, true);
    }

    /**
     * @Rest\View(serializerGroups={"customer"})
     * @Rest\Patch("/customers/{id}")
     */
    public function patchCustomerAction(Request $request)
    {
        return $this->updateCustomer($request, false);
    }

    private function updateCustomer(Request $request, $clearMissing)
    {
        $customer = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Customer')
            ->find($request->get('id')); // L'identifiant en tant que paramètre n'est plus nécessaire
        /* @var $customer Customer */

        if (empty($customer)) {
            return \FOS\RestBundle\View\View::create(['message' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(CustomerType::class, $customer);

        // Le paramètre false dit à Symfony de garder les valeurs dans notre
        // entité si l'utilisateur n'en fournit pas une dans sa requête
        $form->submit($request->request->all(), $clearMissing);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($customer);
            $em->flush();
            return $customer;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT,serializerGroups={"customer"})
     * @Rest\Delete("/customers/{id}")
     */
    public function removeCustomerAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $customer = $em->getRepository('AppBundle:Customer')
            ->find($request->get('id'));
        /* @var $customer Customer */

        if (!$customer) {
            return;
        }

        $em->remove($customer);
        $em->flush();
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED,serializerGroups={"customer"})
     * @Rest\Post("/customers")
     */
    public function postCustomersAction(Request $request)
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($customer);
            $em->flush();
            return $customer;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerGroups={"customer"})
     * @Rest\Get("/customers")
     * @QueryParam(name="offset", requirements="\d+", default="", description="Index de début de la pagination")
     * @QueryParam(name="limit", requirements="\d+", default="", description="Nombre d'éléments à afficher")
     * @QueryParam(name="sort", requirements="(asc|desc)", nullable=true, description="Ordre de tri (basé sur le nom)")
     */
    public function getCustomersAction(Request $request, ParamFetcher $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $sort = $paramFetcher->get('sort');

        $qb = $this->get('doctrine.orm.entity_manager')->createQueryBuilder();
        $qb->select('p')
            ->from('AppBundle:IOT\Customer', 'p');

        if ($offset != "") {
            $qb->setFirstResult($offset);
        }

        if ($limit != "") {
            $qb->setMaxResults($limit);
        }

        if (in_array($sort, ['asc', 'desc'])) {
            $qb->orderBy('p.name', $sort);
        }
        $customer = $qb->getQuery()->getResult();

        return $customer;
    }

    /**
     * @Rest\View(serializerGroups={"customer"})
     * @Rest\Get("/customers/{id}")
     */
    public function getCustomerAction(Request $request)
    {
        $customer = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Customer')
            ->find($request->get('id')); // L'identifiant en tant que paramétre n'est plus nécessaire
        /* @var $customer Customer */

        if (empty($customer)) {
            return \FOS\RestBundle\View\View::create(['message' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        return $customer;
    }
}



