<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }
    #[Route('/list', name: 'app_list')]
    public function usersList(ManagerRegistry $doctrine)
    {
        // function to get all users in the database
        $users = $doctrine->getRepository(User::class)->findAll() ;

        return $this->render('users/usersList.html.twig' , ['users' => $users]) ;
    }

    #[Route('/user/delete/{id}', name: 'app_delete')]
    public function deleteUser($id , ManagerRegistry $doctrine)
    {
        $user = $doctrine->getRepository(User::class)->find($id) ;

        $entityManager = $doctrine->getManager();
        $entityManager->remove($user) ;
        $entityManager->flush() ;

        $users = $doctrine->getRepository(User::class)->findAll() ;

        return $this->render('users/usersList.html.twig' , ['users' => $users]) ;

    }
    #[Route('/user/new', name: 'app_new' , methods: ['POST' , 'GET'] )]
    public function new(ManagerRegistry $doctrine , Request $request) {
        $user = new User();
        $form = $this->createFormBuilder($user)
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('email', TextType::class)
            ->add('save', SubmitType::class, array(
                    'label' => 'Créer')
            )->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_list');

        }
        return $this->render('users/new.html.twig',['form' => $form->createView()]);
    }
    #[Route('/user/update/{id}', name: 'app_new' , methods: ['POST' , 'GET'] )]
    public function update(ManagerRegistry $doctrine , Request $request , $id) {
        $user = new User();
        $user = $doctrine->getRepository(User::class)->find($id);

        $form = $this->createFormBuilder($user)
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('email', TextType::class)
            ->add('save', SubmitType::class, array(
                    'label' => 'Modifier')
            )->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('app_list');

        }
        return $this->render('users/update.html.twig',['form' => $form->createView()]);
    }
}
