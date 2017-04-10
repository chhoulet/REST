<?php

namespace AppBundle\Controller\Place;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Form\PriceType;
use AppBundle\Entity\Price;

class PriceController extends Controller
{
	/**
	*
	* @Rest\View()
	* @Rest\Get("place/{id}/prices")
	*/
	public function getPrices(Request $request)
	{
		$place = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Place')
                ->find($request->get('id')); // L'identifiant en tant que paramétre n'est plus nécessaire
        /* @var $place Place */

        if (empty($place)) {
            return $this->placeNotFound();
        }

        return $place->getPrices();
	}

	/**
	*
	* @Rest\View(statusCode=Response::HTTP_CREATED)
	* @Rest\Post("places/{id}/prices")
	*/
	public function postPrices(Request $request)
	{
		$place = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Place')
                ->find($request->get('id'));
        /* @var $place Place */

        if (empty($place)) {
            return $this->placeNotFound();
        }

        $price = new Price();
        $price->setPlace($place); // Ici, le lieu est associé au prix
        $form = $this->createForm(PriceType::class, $price);

        // Le paramétre false dit à Symfony de garder les valeurs dans notre
        // entité si l'utilisateur n'en fournit pas une dans sa requête
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($price);
            $em->flush();
            return $price;
        } else {
            return $form;
        }
	}

	public function notPlaceExist()
	{
		return \FOS\RestBundle\View\View::create(['message'=>'Place not found'], Response::HTTP_NOT_FOUND);
	}


}