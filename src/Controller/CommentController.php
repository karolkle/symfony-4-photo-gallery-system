<?php


namespace App\Controller;

use App\Entity\Image;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Component\Form\FormTypeInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
/**
 * @Route("/admin")
 * @Security("is_granted('ROLE_ADMIN')")
 */

class CommentController
{
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var CommentRepository
     */
    private $commentRepository;
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

    public function __construct(Environment $twig, CommentRepository $commentRepository, EntityManagerInterface $entityManager, RouterInterface $router, FormFactoryInterface $formFactory)
    {
        $this->twig = $twig;
        $this->commentRepository = $commentRepository;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->formFactory = $formFactory;
    }

    /**
     * @Route("/comments/new", name="comment_new")
     */
    public function commentNew(Request $request, Image $image): Response
    {
        $comment = new Comment();
        $comment->setAuthor($this->getUser());
        $image->addComment($comment);
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return new RedirectResponse(
                $this->router->generate('u')
            );
        }
    }

    /**
     * @Route("/comments", name="comments")
     */
    public function index()
    {
        $html = $this->twig->render('admin/comments.html.twig', [

                'comments' => $this->commentRepository->findBy([], ['id' => 'ASC']),
            ]
        );
        return new Response($html);
    }

    /**
     * @Route("/comments/{id}", name="comments_id")
     */
    public function list(Comment $comment){


        return new Response(
            $this->twig->render('admin/comment.html.twig' ,
                [
                    'comment' => $comment,
                ])
        );
    }

    /**
     * @Route("/comments/edit/{id}", name="comment_edit")
     */
    public function edit(Comment $comment, Request $request){
        $form = $this->formFactory->create(
            CommentType::class,
            $comment
        );
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return new RedirectResponse(
                $this->router->generate('comments')
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
     * @Route("/delete/{id}", name="comment_delete")
     */
    public function delete(Comment $comment){
        $this->entityManager->remove($comment);
        $this->entityManager->flush();
        return new RedirectResponse((
            $this->router->generate('comments')
        ));
    }
}