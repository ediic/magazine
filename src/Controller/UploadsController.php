<?php

namespace App\Controller;

use App\Entity\Uploads;
use App\Form\UploadsType;
use App\Repository\UploadsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File;
use App\Service\FileUploader;

/**
 * @Route("/uploads")
 */
class UploadsController extends AbstractController
{
    /**
     * @Route("/", name="uploads_index", methods={"GET"})
     */
    public function index(UploadsRepository $uploadsRepository): Response
    {
        return $this->render('uploads/index.html.twig', [
            'uploads' => $uploadsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="uploads_new", methods={"GET","POST"})
     */
    public function new(Request $request, FileUploader $fileUploader): Response
    {
        $uploads = new Uploads();
        $form = $this->createForm(UploadsType::class, $uploads);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $uploads->getImage();
            $fileName = $fileUploader->upload($file);

            $uploads->setImage($fileName);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($uploads);
            $entityManager->flush(); 

            return $this->redirectToRoute('uploads_index');
        }

        return $this->render('uploads/new.html.twig', [
            'uploads' => $uploads,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="uploads_show", methods={"GET"})
     */
    public function show(Uploads $uploads): Response
    {
        return $this->render('uploads/show.html.twig', [
            'upload' => $uploads,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="uploads_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Uploads $uploads, FileUploader $fileUploader): Response
    {
        $uploads->setImage(new File(
            $this->getParameter('image_directory').'/'.$uploads->getImage()
            ));

        $form = $this->createForm(UploadsType::class, $uploads);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $uploads->getImage();
            $fileName = $fileUploader->upload($file);

            $uploads->setImage($fileName);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('uploads_index', [
                'id' => $uploads->getId(),
            ]);
        }

        return $this->render('uploads/edit.html.twig', [
            'upload' => $uploads,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="uploads_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Uploads $uploads): Response
    {
        if ($this->isCsrfTokenValid('delete'.$uploads->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($uploads);
            $entityManager->flush();
        }

        return $this->redirectToRoute('uploads_index');
    }
}
