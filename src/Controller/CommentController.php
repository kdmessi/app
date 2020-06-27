<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use DateTime;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/", name="comment_index", methods={"GET"})
     *
     * @param CommentRepository  $commentRepository
     * @param PaginatorInterface $paginator
     * @param Request            $request
     *
     * @return Response
     */
    public function index(CommentRepository $commentRepository, PaginatorInterface $paginator, Request $request): Response
    {
        return $this->render('comment/index.html.twig', [
            'comments' => $paginator->paginate($commentRepository->findAll(), $request->get('page', 1), 10),
        ]);
    }

    /**
     * @Route("/new/{id}", name="comment_new", methods={"GET","POST"},requirements={"id": "[1-9]\d*"})
     *
     * @param Request           $request
     * @param Book              $book
     *
     * @param CommentRepository $repository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function new(Request $request, Book $book, CommentRepository $repository): Response
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new DateTime('now'));
            $comment->setBook($book);
            $repository->save($comment);

            $this->addFlash('success', 'created_successfully');

            return $this->redirectToRoute('book_show', ['id' => $book->getId()]);
        }

        return $this->render('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="comment_show", methods={"GET"},requirements={"id": "[1-9]\d*"})
     *
     * @param Comment $comment
     *
     * @return Response
     */
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="comment_edit", methods={"GET","POST"},requirements={"id": "[1-9]\d*"})
     *
     * @param Request           $request
     * @param Comment           $comment
     *
     * @param CommentRepository $repository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function edit(Request $request, Comment $comment, CommentRepository $repository): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($comment);

            $this->addFlash('success', 'updated_successfully');

            return $this->redirectToRoute('comment_index');
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="comment_delete", methods={"DELETE","GET"}, requirements={"id": "[1-9]\d*"})
     *
     * @param Request           $request
     * @param Comment           $comment
     *
     * @param CommentRepository $repository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Request $request, Comment $comment, CommentRepository $repository): Response
    {
        $form = $this->createForm(FormType::class, $comment, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->remove($comment);

            $this->addFlash('success', 'deleted_successfully');

            return $this->redirectToRoute('book_show', ['id' => $comment->getBookId()]);
        }

        return $this->render('comment/delete.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }
}
