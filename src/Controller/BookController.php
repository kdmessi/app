<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Repository\CommentRepository;
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
     */
    public function index(BookRepository $bookRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $term = $request->get('genre');
        $validator = Validation::createValidator();
        $violations = $validator->validate($term, [
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
                $bookRepository->queryByGenreLike($request->get('genre')),
                $request->get('page', 1),
                10
            ),
        ]);
    }

    /**
     * @Route("/new", name="book_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book->setCreatedAt(new \DateTime('now'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();

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
     */
    public function show(Book $book, Request $request, CommentRepository $commentRepository, PaginatorInterface $paginator): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
            'comments' => $paginator->paginate(
                $commentRepository->findBy(['bookId' => $book->getId()]),
                $request->get('page', 1), 10
            ),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="book_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Book $book): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();

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
     */
    public function delete(Request $request, Book $book): Response
    {
        $form = $this->createForm(FormType::class, $book, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($book);
            $entityManager->flush();

            $this->addFlash('success', 'book_deleted');

            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/delete.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }
}
