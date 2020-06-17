<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/author")
 */
class AuthorController extends AbstractController
{
    /**
     * @Route("/", name="author_index", methods={"GET"})
     */
    public function index(AuthorRepository $authorRepository,PaginatorInterface $paginator,Request $request): Response
    {
        return $this->render('author/index.html.twig', [
            'authors' => $paginator->paginate(
                $authorRepository->findAll(),
                $request->get('page',1),
                10
            )
        ]);
    }

    /**
     * @Route("/new", name="author_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($author);
            $entityManager->flush();

            $this->addFlash('success', 'created_successfully');

            return $this->redirectToRoute('author_index');
        }

        return $this->render('author/new.html.twig', [
            'author' => $author,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="author_show", methods={"GET"})
     */
    public function show(Author $author): Response
    {
        return $this->render('author/show.html.twig', [
            'author' => $author,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="author_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Author $author): Response
    {
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'updated_successfully');

            return $this->redirectToRoute('author_index');
        }

        return $this->render('author/edit.html.twig', [
            'author' => $author,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/delete/{id}", name="author_delete", methods={"DELETE","GET"}, requirements={"id": "[1-9]\d*"})
     */
    public function delete(Request $request, Author $author, TranslatorInterface $translator): Response
    {

        $form = $this->createForm(FormType::class, $author, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$author->getBooks()->isEmpty()) {
                $this->addFlash('danger',$translator->trans('You can not remove author with books'));
                return $this->redirectToRoute('author_delete',['id'=>$author->getId()]);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($author);
            $entityManager->flush();

            $this->addFlash('success', 'deleted_successfully');

            return $this->redirectToRoute('author_index');
        }

        return $this->render('author/delete.html.twig', [
            'author' => $author,
            'form' => $form->createView(),
        ]);
    }
}
