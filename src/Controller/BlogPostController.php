<?php


namespace App\Controller;

use App\Entity\Comment;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\UserType;
use App\Repository\CommentRepository;
use App\Repository\ImageRepository;
use App\Repository\UserRepository;
use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class BlogPostController extends AbstractController
{
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var ImageRepository
     */
    private $imageRepository;
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
    /**
     * @var CommentRepository
     */
    private $commentRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(Environment $twig, CommentRepository $commentRepository, UserRepository $userRepository, ImageRepository $imageRepository, EntityManagerInterface $entityManager, RouterInterface $router, FormFactoryInterface $formFactory)
    {
        $this->twig = $twig;
        $this->imageRepository = $imageRepository;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->formFactory = $formFactory;
        $this->commentRepository = $commentRepository;
        $this->userRepository = $userRepository;
    }


    /**
     * @Route("/homi/{id}", name="homesy")
     */
    public function indexxx(Image $image, Comment $comment)
    {
        $html = $this->twig->render('home/photo.html.twig', [
                'image' => $image
            ]
        );
        return new Response($html);
    }

     /**
     * @Route("/", name="homes")
     */
    public function index()
    {
        $html = $this->twig->render('home/home.html.twig', [
                'images' => $this->imageRepository->findBy([], ['id' => 'ASC']),
            ]
        );
        return new Response($html);
    }

    /**
     * @Route("/home/{id}", name="home_id")
     * @ParamConverter("id", class="App\Entity\Image")
     */
    public function list(Image $image, Request $request){
        $commenty = new Comment();
     //   $image_com = $this->getDoctrine()->getManager()->getRepository(Image::class)->find($image->getId());
        $image_com = $this->getDoctrine()->getManager()->getRepository(Image::class)->find($image->getId());


        $commenty->setImageId($image_com);
        $commenty->setAuthor($this->getUser());
        $form = $this->createForm(
            CommentType::class,
            $commenty
        );
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($commenty);
            $entityManager->flush();

            
        }
        return new Response(
            $this->twig->render('home/homes.html.twig' ,
                [
                    'comments'  => $this->commentRepository->findBy([], ['id' => 'ASC']),
                    'form' => $form->createView(),
                    'image' => $image,

                ])
        );
    }


    /**
     * @Route("/admin/dashboard", name="dashboard")
     * @Security("is_granted('ROLE_ADMIN')")
     */

    public function admin()
    {
        return $this->render('admin/admin.html.twig', [
        'images' => $this->imageRepository->findBy([], ['id' => 'ASC']),
        'users' => $this->userRepository->findBy([], ['id' => 'ASC']),
        'comments' => $this->commentRepository->findBy([], ['id' => 'ASC']),
            ]
        );
    }
}