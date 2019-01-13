<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    /**
     * @Route("/users", name="user_list")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:User')
            ->createQueryBuilder('a');

        if($request->query->getAlnum('filter'))
        {
            $queryBuilder
                ->where('a.username LIKE :username OR a.name LIKE :name OR a.email LIKE :email OR a.description LIKE :description')
                // ->where('a.name LIKE :name')
                // ->where('a.email LIKE :email')
                // ->where('a.description LIKE :description')
                ->groupBy('a.id')
                ->setParameters([
                    'username' => '%' . $request->query->getAlnum('filter') . '%',
                    'name' => '%' . $request->query->getAlnum('filter') . '%',
                    'email' => '%' . $request->query->getAlnum('filter') . '%',
                    'description' => '%' . $request->query->getAlnum('filter') . '%'
                ]);
        }

        $query = $queryBuilder->getQuery();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('@AppBundle/user/index.html.twig', [
            'users' => $pagination
        ]);
    }

    /**
     * @Route("/user/edit/{id}", name="user_edit")
     */
    public function editAction($id, Request $request)
    {
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:User');
        $user = $repository->find($id);

        $form = $this->createForm(UserType::class, $user, [

        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User successfully updated!');

            return $this->redirectToRoute('user_list');

        }

        return $this->render('@AppBundle/user/edit.html.twig', [
            'edit_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/delete/{id}", name="user_delete")
     */
    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:User');
        $user = $repository->find($id);

        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'User successfully deleted!');

        return $this->redirectToRoute('user_list');
    }

    /**
     * @Route("/user/updatefield", name="user_update_field")
     */
    public function updateFieldAction(Request $request)
    {
        if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1)
        {
            $em = $this->getDoctrine()->getManager();

            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:User');
            $user = $repository->find($request->get('pk'));
            

            switch ($request->get('name')) {
                case 'name':
                    $user->setName($request->get('value'));
                    break;

                case 'username':
                    $user->setUsername($request->get('value'));
                    break;

                case 'email':
                    $user->setEmail($request->get('value'));
                    break;

                case 'gender':
                    $user->setGender($request->get('value'));
                    break;

                case 'description':
                    $user->setDescription($request->get('value'));
                    break;
            }

            $violations = $this->get('validator')
                ->validate($user);
            
            if (count($violations)) {
                foreach ($violations as $key => $value) {
                    $errMsg[] = $value->getMessage();
                }
                $response = new JsonResponse(['errors' => $errMsg], 400);
                return $response;
            }
            else {
                try {
                    $em->persist($user);
                    $em->flush();
                } catch(\Doctrine\DBAL\DBALException $e) {
                    $response = new JsonResponse(['errors' => 'Username or Email must be unique.'], 400);
                    return $response;
                }

                $response = new JsonResponse(['success' => true]);
                return $response;
            }
        }
    }
}
