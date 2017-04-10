<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Entity\Place;
use AppBundle\Form\PlaceType;

class PlaceController extends Controller
{
    
    public function getPlacesAction(Request $request)
    {
        $places = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Place')
                ->findAll();
        /* @var $places Place[] */

        if (empty($places)) {
            return new JsonResponse(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
        }

        $formatted = [];
        foreach ($places as $place) {
            $formatted[] = [
               'id' => $place->getId(),
               'name' => $place->getName(),
               'address' => $place->getAddress(),
            ];
        }

        return new JsonResponse($formatted);
    }

   
    public function getPlaceAction(Request $request)
    {
        $place = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Place')
                ->find($request->get('place_id'));

        $formatted=[
            "id"=>$place->getId(),
            "name"=>$place->getName(),
            "adress"=>$place->getAdress()
        ];

        return new JsonResponse($formatted);
    }

    /**
    *
    * @Rest\View()
    * @Rest\Put("places/{id}")
    */
    public function updatePlaceAction(Request $request, $clearMissing)
    {
        $em=$this->getDoctrine()->getManager();
        $resource=$em->getRepository->find($request->get('id'));

        if(!$resource)
        {
            return new JsonResponse(['message'=>'Not existant resource'], Response::HTTP_NOT_FOUND);            
        }

        $form->$this->createForm(PlaceType::class, $resource);

        $form->submit($request->request->all(), $clearMissing);

        if($form->isValid())
        {
            $em->flush();
            return $resource;
        }
        else
        {
            return $form;
        }
    }

    /**
    *
    * @Rest\View()
    * @Rest\Put("places/{id}")
    */
    public function partialUpdatePlaceAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $updatingResource=$em->getRepository('AppBundle:Place')->find($request->get('id'));

        if(!$updatingResource)
        {
            return new JsonResponse(['message'=>'Ressource inexistante'], Response:HTTP_NOT_FOUND);
        }

        $form=$this->createForm(PlaceType::class, $updatingResource);

        $form->submit($request->request->all(),false);
        //Mise à false de l'option 'clearMissing, signifiant la suppression de l'existant lorsqu'un attribut est manquant dans la requete.
        //Sa mise à false permet de conserver ce qui existe, d'où succes d'une mise à jour partielle.

        if($form->isValid())
        {
            $em->flush();
            return $updatingResource;
        }
        else
        {
            return $form;
        }
    }

    //Le corps de cette fonction peut aussi être remplacé par:
    /**
    *
    * @Rest\View()
    * @Rest\Patch("places/{id}")
    */
    public function partialUpdatePlaceAction(Request $request)
    {
        return $this->updatePlaceAction($request, false);
    }


    public function updatePlace(Request $request, $clearMissing)
    {
        $em=$this->getDoctrine()_>getManager();
        $place=$em->getRepository('AppBundle:Place')->find($request->get('id'));

        if(!$place)
        {
            return new JsonResponse(['message'=>'place Not Found'], Response::HTTP_NOT_FOUND);
        }

        $form=$this->createForm(PlaceType::class, $place);

        $form->submit($request->request->all(), $clearMissing);

        if($form->isValid())
        {
            $em->flush();
            return $place;
        }
        else
        {
            return $form;
        }
    }

    /*
    *
    * @Rest\View()
    * @Rest\Put("places/{id}")
    *
    */
    public function totalUpdateAction(Request $request)
    {
        return $this->updatePlace($request, true);
    }

    /**
    *
    * @Rest\View()
    * @Rest\Patch("places/{id}")
    */
    public function partialUpdateAction(Request $request)
    {
        return $this->updatePlace($request, false);
    }
}