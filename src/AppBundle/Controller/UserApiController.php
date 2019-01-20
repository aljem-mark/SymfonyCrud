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
     * @Route("/api/users", name="api_user_list", options = { "expose" = true })
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
     * @Route("/api/user/create", name="api_user_create", options = { "expose" = true })
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

    /**
     * @Route("/api/user/{id}", name="api_user_edit", options = { "expose" = true })
     */
    public function editAction($id, Request $request)
    {
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:User');
        $user = $repository->find($id);

        if($user) 
        {
            $response = new JsonResponse([
                'user' => [
                    'name' => $user->getName(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'gender' => $user->getGender(),
                    'description' => $user->getDescription(),
                ],
            ], 200);
    
            return $response;
        }

        $response = new JsonResponse(['errors' => ['Error']], 400);

        return $response;
    }

    /**
     * @Route("/api/user/update/{id}", name="api_user_update", options = { "expose" = true })
     */
    public function updateAction($id, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:User');
        $user = $repository->find($id);

        $form = $this->createForm(UserType::class, $user);

        $form->submit($data);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();

            return new JsonResponse(['success' => 'User successfully updated.'], 200);
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

    /**
     * @Route("/api/user/delete/{id}", name="api_user_delete", options = { "expose" = true })
     */
    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:User');
        $user = $repository->find($id);

        $em->remove($user);
        $em->flush();

        $response = new JsonResponse(['success' => 'User successfully deleted.'], 200);

        return $response;
    }
}