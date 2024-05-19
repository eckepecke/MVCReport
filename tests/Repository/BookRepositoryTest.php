<?php

namespace App\Repository;



use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;



class BookRepositoryTest extends TestCase
{
    public function testProcessBookFromRequest()
    {
        // Mock the Request object
        $requestData = [
            'title' => 'Test Book',
            'isbn' => '1234567890',
            'author' => 'Test Author',
            'image_name' => 'test.jpg',
            'description' => 'Test description',
            'year' => '2022'
        ];

        $request = new Request([], $requestData);

        // Mock ManagerRegistry
        $managerRegistryMock = $this->createMock(ManagerRegistry::class);

        // Create a BookRepository instance with the mocked ManagerRegistry
        $bookRepository = new BookRepository($managerRegistryMock);


        // Call the method to process the book from the request
        $book = $bookRepository->processBookFromRequest($request);

        // Assert that the Book object is updated with the data from the request
        $this->assertEquals($requestData['title'], $book->getTitle());
        $this->assertEquals($requestData['isbn'], $book->getIsbn());
        $this->assertEquals($requestData['author'], $book->getAuthor());
        $this->assertEquals($requestData['image_name'], $book->getImg());
        $this->assertEquals($requestData['description'], $book->getDescription());
        $this->assertEquals($requestData['year'], $book->getPublishedYear());
    }

    // public function testFindAllBooks()
    // {
    //     // Mock ManagerRegistry
    //     $managerRegistryMock = $this->createMock(ManagerRegistry::class);

    //     // Create a BookRepository instance with the mocked ManagerRegistry
    //     $bookRepository = new BookRepository($managerRegistryMock);

    //     $res = $bookRepository->findAllBooks();
    //     $exp = [];

    //     $this->assertSame($res, $exp);
    // }
}