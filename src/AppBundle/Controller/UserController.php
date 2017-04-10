<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Reques
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;

class UserController extends Controller
{
	/**
     * @Route("/users", name="users_list")
     * @Method({"GET"})
     */
	public function getUsersAction(Request $request)
	{
		$users = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:User')
                ->findAll();

        if (empty($users)) {
            return new JsonResponse(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
        }

		$formatted=[];
		foreach($users as $user)
		{
			$formatted[]=[
				"id"=>$user->getId(),
				"firstname"=>$user->getFirstname(),
				"lastname" =>$user->getLastname(),
				"email"    =>$user->getEmail()
			];
		}

		return new JsonResponse($formatted);
	}

	/**
	* @Route("/users/{id}", name="users_one")
	* @Method({"GET"})
	*/
	public function getUserAction(Request $request)
	{
		$user = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:User')
                ->find($request->get('id'));

        $formatted = [
           'id' => $user->getId(),
           'firstname' => $user->getFirstname(),
           'lastname' => $user->getLastname(),
           'email' => $user->getEmail(),
        ];

        return new JsonResponse($formatted);
	}

	public function updateUser(Request $request, $clearMissing)
	{
		$em=$this->getDoctrine()_>getManager();
		$updateUser=$em->getRepository('AppBundle:User')->find($request->get('id'));

		if(!$updateUser)
		{
			return new JsonResponse(['message'=>'User not found'], Response::HTTP_NOT_FOUND);
			// A remplacer par :
			return FOS\RestBundle\View\View::create(['message'=>'User not found'], Response::HTTP_NOT_FOUND);
		}

		$form=$this->createForm(UserType::class, $updateUser);
		$form->submit($request->request->all(), $clearMissing);

		if($form->isValid())
		{
			$em_>persist($updateUser);
			$em->flush();
			return $updateUser;
		}
		else
		{
			return $form;
		}
	}

	/**
	*
	* @Rest\View()
	* @Rest\Put("users/{id}")
	*
	*/
		public function totalUpdateAction(Request $request)
		{
			return $this->updateUser($request, true);
		}

	/**
	*
	* @Rest\View()
	* @Rest\Patch("users/{id}")
	*/
	public function partialUpdateAction(Request $request)
	{
		return $this->updateUser($request, false);
	}
}