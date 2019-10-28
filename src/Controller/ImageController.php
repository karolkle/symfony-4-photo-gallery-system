<?php


namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/admin")
 * @Security("is_granted('ROLE_ADMIN')")
 */

class ImageController extends AbstractController
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

    public function __construct(Environment $twig, ImageRepository $imageRepository, EntityManagerInterface $entityManager, RouterInterface $router, FormFactoryInterface $formFactory)
    {
        $this->twig = $twig;
        $this->imageRepository = $imageRepository;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->formFactory = $formFactory;
    }
    /**
     * @Route("/uploads", name="upload")
     */
    public function new(Request $request)
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $file = $form->get('file')->getData();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move($this->getParameter('uploads_directory'), $fileName);
            $image->setFileName($fileName);
            $image->setUploadDirectory($this->getParameter('uploads_directory')."/".$fileName);
            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();
            return $this->redirectToRoute('images');
        }


        return $this->render('admin/upload.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/images", name="images")
     */
    public function index()
    {
        $html = $this->twig->render('admin/images.html.twig', [
                'images' => $this->imageRepository->findBy([], ['id' => 'ASC']),
            ]
        );
        return new Response($html);
    }

    /**
     * @Route("/images/{id}", name="image_id")
     */
    public function list(Image $image){


        return new Response(
            $this->twig->render('admin/image.html.twig' ,
                [
                    'image' => $image,
                ])
        );
    }

    /**
     * @Route("/images/edit/{id}", name="image_edit")
     */
    public function edit(Image $image, Request $request){
        $form = $this->formFactory->create(ImageType::class, $image);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
          $file = $form->get('file')->getData();

            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move($this->getParameter('uploads_directory'), $fileName);
            $image->setFileName($fileName);
            $image->setUploadDirectory($this->getParameter('uploads_directory')."/".$fileName);
            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();
            return $this->redirectToRoute('images');
        }
        return new Response(
            $this->twig->render(
                'admin/upload.html.twig',
                ['form' => $form->createView()]
            )
        );

    }

    /**
     * @Route("/images/delete/{id}", name="image_delete")
     */
    public function delete(Image $image){
        $this->entityManager->remove($image);
        $this->entityManager->flush();
        return new RedirectResponse((
        $this->router->generate('images')
        ));
    }
}