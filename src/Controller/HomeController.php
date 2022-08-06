<?php

namespace App\Controller;

use App\Entity\User;
use http\Client\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
