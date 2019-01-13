<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class RegistrationController extends Controller
{
    /**
     * @Route("/register", name="registration")
     * @return \Symfony\Component\HttpFoundation\Request
     * @throws \LogicException
     */
    public function registerAction(Request $request)
    {
        $user = new User;

        $form = $this->createForm(UserType::class, $user, [

        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

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

            $this->addFlash('success', 'You are now successfully registered!');

            return $this->redirectToRoute('user_list');

        }

        return $this->render('@AppBundle/registration/register.html.twig', [
            'registration_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register-modal", name="registration_modal")
     * @return \Symfony\Component\HttpFoundation\Request
     * @throws \LogicException
     */
    public function modalAction(Request $request)
    {
        $user = new User;

        $form = $this->createForm(UserType::class, $user, [
            'action' => $this->generateUrl('registration_modal_save'),
            'attr' => [
                'id' => 'registration-modal-save'
            ]
        ]);

        return $this->render('@AppBundle/registration/modal.html.twig', [
            'modal_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register-modal-save", name="registration_modal_save")
     * @return \Symfony\Component\HttpFoundation\Request
     * @throws \LogicException
     */
    public function modalSaveAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(array('message' => 'You can access this only using Ajax!'), 400);
        }

        $user = new User;

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        $password = $this
            ->get('security.password_encoder')
            ->encodePassword(
                $user,
                $user->getPlainPassword()
            );

        $user->setPassword($password);

        $em = $this->getDoctrine()->getManager();

        $violations = $this->get('validator')
            ->validate($user);
        
        if (count($violations))
        {
            foreach ($violations as $key => $value) {
                $errMsg[] = "<li>" . $value->getMessage() . "</li>";
            }
            $response = new JsonResponse(['message' => "<div class=\"alert alert-danger\"><ul>" . implode($errMsg) . "</ul></div>"], 400);
            return $response;
        }
        else
        {
            try {
                $em->persist($user);
                $em->flush();
            } catch(\Doctrine\DBAL\DBALException $e) {
                return new JsonResponse(['message' => 'Username or Email must be unique.'], 400);
            }

            return new JsonResponse(['message' => 'You are now successfully registered!'], 200);
        }

        $response = new JsonResponse(['message' => 'Error'], 400);

        return $response;
    }
}
