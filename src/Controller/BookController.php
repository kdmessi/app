<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Repository\CommentRepository;
use DateTime;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Validation;

/**
 * @Route("/book")
 */
class BookController extends AbstractController
{
    /**
     * @Route("/", name="book_index", methods={"GET"})
     *
     * @param BookRepository     $bookRepository
     * @param PaginatorInterface $paginator
     * @param Request            $request
     *
     * @return Response
     */
    public function index(BookRepository $bookRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $genre = $request->query->getAlnum('genre');
        $validator = Validation::createValidator();
        $violations = $validator->validate($genre, [
            new Length(['max' => 10]),
        ]);

        if (0 !== count($violations)) {
            $this->addFlash('danger', 'invalid data');

            return $this->render('book/index.html.twig', [
                'books' => $paginator->paginate(
                    $bookRepository->queryByGenreLike(null),
                    $request->get('page', 1),
                    10
                ),
            ]);
        }

        return $this->render('book/index.html.twig', [
            'books' => $paginator->paginate(
                $bookRepository->queryByGenreLike($genre),
                $request->get('page', 1),
                10
            ),
        ]);
    }

    /**
     * @Route("/new", name="book_new", methods={"GET","POST"})
     *
     * @param Request        $request
     *
     * @param BookRepository $repository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function new(Request $request, BookRepository $repository): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book->setCreatedAt(new DateTime('now'));
            $repository->save($book);

            $this->addFlash('success', 'book_created');

            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/new.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="book_show", methods={"GET"})
     *
     * @param Book               $book
     * @param Request            $request
     * @param CommentRepository  $commentRepository
     * @param PaginatorInterface $paginator
     *
     * @return Response
     */
    public function show(Book $book, Request $request, CommentRepository $commentRepository, PaginatorInterface $paginator): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
            'comments' => $paginator->paginate(
                $commentRepository->findBy(['bookId' => $book->getId()]),
                $request->get('page', 1),
                10
            ),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="book_edit", methods={"GET","POST"})
     *
     * @param Request        $request
     * @param Book           $book
     *
     * @param BookRepository $repository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function edit(Request $request, Book $book, BookRepository $repository): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($book);

            $this->addFlash('success', 'updated_successfully');

            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="book_delete", methods={"DELETE","GET"}, requirements={"id": "[1-9]\d*"})
     *
     * @param Request        $request
     * @param Book           $book
     *
     * @param BookRepository $repository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Request $request, Book $book, BookRepository $repository): Response
    {
        $form = $this->createForm(FormType::class, $book, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->remove($book);

            $this->addFlash('success', 'book_deleted');

            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/delete.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }
}
