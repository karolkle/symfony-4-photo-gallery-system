<?php


namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Form\FormTypeInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
/**
 * @Route("/admin")
 * @Security("is_granted('ROLE_ADMIN')")
 */

class UserController
{
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function __construct(Environment $twig, UserRepository $userRepository, EntityManagerInterface $entityManager, RouterInterface $router, FormFactoryInterface $formFactory)
    {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->formFactory = $formFactory;
    }

    /**
     * @Route("/users", name="users")
     */
    public function index()
    {
        $html = $this->twig->render('admin/index.html.twig', [
             //    'posts' => $this->UserRepository->findAll()
                'users' => $this->userRepository->findBy([], ['id' => 'ASC']),
            ]
        );
        return new Response($html);
    }

    /**
     * @Route("/users/{id}", name="users_id")
     */
    public function list(User $user){


        return new Response(
            $this->twig->render('admin/users.html.twig' ,
                [
                    'user' => $user,
                ])
        );
    }

    /**
     * @Route("/users/edit/{id}", name="user_edit")
     */
    public function edit(User $user, Request $request){
        $form = $this->formFactory->create(
            UserType::class,
            $user
        );
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return new RedirectResponse(
                $this->router->generate('users')
            );
        }
           return new Response(
               $this->twig->render(
                   'register/register.html.twig',
                   ['form' => $form->createView()]
               )
           );

    }

    /**
     * @Route("/delete/{id}", name="user_delete")
     */
    public function delete(User $user){
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        return new RedirectResponse((
            $this->router->generate('users')
        ));
    }
}