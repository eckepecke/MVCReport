<?php

namespace App\Controller;

use App\Entity\Book;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\BookRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/library/show_one/{id}', name: 'read_one')]
    public function readOne(
        BookRepository $bookRepository,
        int $id
    ): Response {
        $book = $bookRepository
            ->find($id);
        $idArray = $bookRepository
            ->findAllIds();

        $lastId = end($idArray);
        $lastId = $lastId["id"];

        $firstId = reset($idArray);
        $firstId = $firstId["id"];

        $idIndex = null;

        foreach ($idArray as $key => $item) {
            if ($item['id'] === $id) {
                $idIndex = $key;
                break;
            }
        }

        $nextId = null;
        $prevId = null;

        if (isset($idArray[$idIndex + 1]["id"])) {
            $nextId = $idArray[$idIndex + 1]["id"];
        }

        if (isset($idArray[$idIndex - 1]["id"])) {
            $prevId = $idArray[$idIndex - 1]["id"];
        }

        return $this->render('library/one_book.html.twig', [
            'book' => $book,
            'lastId' => $lastId,
            'firstId' => $firstId,
            'nextId' => $nextId,
            'prevId' => $prevId,
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
        BookRepository $bookRepository,
        Request $request
    ): Response {
        $book = $bookRepository->processBookFromRequest($request);

        $entityManager = $doctrine->getManager();
        $entityManager->persist($book);
        $entityManager->flush();

        return $this->redirectToRoute('read_many');
    }



    #[Route('/library/update/{id}', name: 'update_book')]
    public function updateBook(
        BookRepository $bookRepository,
        int $id
    ): Response {
        $book = $bookRepository
            ->find($id);


        return $this->render('library/update_form.html.twig', [
            'book' => $book,
            'controller_name' => 'LibraryController'
        ]);
    }

    #[Route('/library/update_data', name: 'update_data', methods: ["POST"])]
    public function updateBookData(
        ManagerRegistry $doctrine,
        BookRepository $bookRepository,
        Request $request
    ): Response {
        $id = $request->request->get('id');
        $book = $bookRepository->find($id);
        if ($book === null) {
            throw new Exception("Book with id $id not found.");
        }

        $book = $bookRepository->processBookFromRequest($request, $book);

        $entityManager = $doctrine->getManager();
        $entityManager->persist($book);
        $entityManager->flush();

        return $this->redirectToRoute('read_one', ['id' => $id]);
    }

    #[Route('/library/delete_book{id}', name: 'delete_book', methods: ["POST"])]
    public function deleteBook(
        ManagerRegistry $doctrine,
        BookRepository $bookRepository,
        int $id
    ): Response {

        $book = $bookRepository->find($id);

        $entityManager = $doctrine->getManager();
        $entityManager->remove($book);
        $entityManager->flush();

        return $this->redirectToRoute('read_many');
    }

    #[Route("/api/library/books", name: "api_library", methods: ["POST", "GET"])]
    public function apiLibrary(
        BookRepository $bookRepository
    ): Response {
        $books = $bookRepository
            ->findAll();

        if (empty($books)) {
            throw new Exception("No books in library!");
        }

        $bookArray = [];

        foreach ($books as $book) {
            $bookArray[] = $book->getAttributesAsArray();
        }

        $response = new JsonResponse($bookArray);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/library/book/{isbn}", name: "api_book", methods: ["POST", "GET"])]
    public function apiBook(
        BookRepository $bookRepository,
        string $isbn
    ): Response {
        $book = $bookRepository
            ->findByISBN($isbn);

        if (!$book) {
            throw new Exception("No book with that number!");
        }


        $bookData = $book->getAttributesAsArray();


        $response = new JsonResponse($bookData);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }
}
