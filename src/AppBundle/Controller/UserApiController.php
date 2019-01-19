<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
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
        $data = json_decode($request->getContent(), true);
        unset($data['plainPassword2']);

        $user = new User;

        $form = $this->createForm(UserType::class, $user);

        $form->submit($data);

        if ($form->isValid())
        {
            $password = $this
                ->get('security.password_encoder')
                ->encodePassword(
                    $user,
                    $user->getPlainPassword()
                );

            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();

            return new JsonResponse(['success' => 'You are now successfully registered!'], 200);
        }
        else
        {
            $errors = array();
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            $response = new JsonResponse(['errors' => $errors], 400);
            return $response;
        }

        $response = new JsonResponse(['errors' => ['Error']], 400);

        return $response;
    }
}