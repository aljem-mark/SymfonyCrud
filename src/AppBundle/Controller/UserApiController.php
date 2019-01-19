<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserApiController extends Controller
{
    /**
     * @Route("/api/users", name="api_user_list")
     */
    public function indexAction(Request $request)
    {
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:User');
        $users = $repository->findAll();

        $usersData = [];
        foreach ($users as $key => $value) {
            $usersData[] = [
                'id' => $value->getId(),
                'name' => $value->getName(),
                'username' => $value->getUsername(),
                'email' => $value->getEmail(),
                'gender' => ( $value->getGender() == 'm' ) ? 'Male' : 'Female',
                'description' => $value->getDescription(),
            ];
        }

        return new JsonResponse([
            'status' => true,
            'users' => $usersData
        ], 200);
    }

    
    /**
     * @Route("/api/user/create", name="api_user_create")
     */
    public function createAction(Request $request)
    {
    }
}