<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(Request $request,Security $security,UserPasswordEncoderInterface $encoder,UserRepository $userRepository):Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);
        $user=new User();
        $form=$this->createForm(AdminType::class,$user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $loggedUser = $security->getUser();

            if($encoder->isPasswordValid($loggedUser,$user->getConfirm()))
            {
                $dbUser = $userRepository->findAll()[0];
                $dbUser->setPassword($encoder->encodePassword($dbUser,$user->getPassword()));
                $dbUser->setEmail($user->getEmail());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($dbUser);
                $entityManager->flush();
                $this->addFlash('success','updated_successfully');
                return $this->redirectToRoute('book_index');
            }
            $this->addFlash('danger','invalid_confirm_password');
        }


        return $this->render('admin/index.html.twig', [
            'form'=>$form->createView(),
        ]);
    }
}