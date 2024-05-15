<?php

namespace App\Controller;

use App\Entity\Book;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\BookRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class LibraryController extends AbstractController
{
    #[Route('/library', name: 'library')]
    public function index(): Response
    {
        return $this->render('library/index.html.twig', [
            'controller_name' => 'LibraryController',
        ]);
    }

    #[Route('/library/read_many', name: 'read_many')]
    public function showAllBooks(
        BookRepository $bookRepository
    ): Response {
        $books = $bookRepository
            ->findAll();

        return $this->render('library/read_many.html.twig', [
            'books' => $books,
            'controller_name' => 'LibraryController'
        ]);
    }

    #[Route('/library/show/{id}', name: 'read_one')]
    public function readOne(
        BookRepository $bookRepository,
        int $id
    ): Response {
        $book = $bookRepository
            ->find($id);

        return $this->render('library/one_book.html.twig', [
            'book' => $book,
            'controller_name' => 'LibraryController'
        ]);
    }

    #[Route('/library/add_form', name: 'add_form')]
    public function addForm(): Response
    {
        return $this->render('library/add_form.html.twig', [
            'controller_name' => 'LibraryController',
        ]);
    }

    #[Route('/library/create', name: 'create_book', methods: ["POST"])]
    public function createBook(
        ManagerRegistry $doctrine,
        Request $request
    ): Response {
        $title = $request->request->get('title');
        $isbn = $request->request->get('isbn');
        $author = $request->request->get('author');
        $img = $request->request->get('image_name');
        $description = $request->request->get('description');
        $year = $request->request->get('year');

        $entityManager = $doctrine->getManager();

        $book = new Book();
        if (!empty($title)) {
            $book->setTitle($title);
        }
        if (!empty($isbn)) {
            $book->setIsbn($isbn);
        }
        if (!empty($author)) {
            $book->setAuthor($author);
        }
        if (!empty($img)) {
            $book->setImg($img);
        }
        if (!empty($description)) {
            $book->setDesription($description);
        }
        if (!empty($year)) {
            $book->setPublicationYear($year);
        }
        // tell Doctrine you want to (eventually) save the Product
        // (no queries yet)
        $entityManager->persist($book);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new book with id '.$book->getId());
    }


    #[Route('/library/update', name: 'update_book', methods: ["POST"])]
    public function updateBook(
        ManagerRegistry $doctrine
    ): Response {
        // $title = $request->request->get('title');
        // $isbn = $request->request->get('isbn');
        // $author = $request->request->get('author');
        // $img = $request->request->get('image_name');
        // $description = $request->request->get('description');
        // $year = $request->request->get('year');

        // $entityManager = $doctrine->getManager();

        // $book = new Book();
        // if (!empty($title)) {
        //     $book->setTitle($title);
        // }
        // if (!empty($isbn)) {
        //     $book->setIsbn($isbn);
        // }
        // if (!empty($author)) {
        //     $book->setAuthor($author);
        // }
        // if (!empty($img)) {
        //     $book->setImg($img);
        // }
        // if (!empty($description)) {
        //     $book->setDesription($description);
        // }
        // if (!empty($year)) {
        //     $book->setPublicationYear($year);
        // }
        // $entityManager = $doctrine->getManager();
        // $book = $entityManager->getRepository(Book::class)->find($id);
        // if (!$book) {
        //     throw $this->createNotFoundException(
        //         'No book found for id '.$id
        //     );
        // }

        // $book->setPublishedYear($year);
        // $entityManager->flush();

        return $this->redirectToRoute('create_book');
    }
}