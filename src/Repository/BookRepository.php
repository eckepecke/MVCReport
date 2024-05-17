<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function findAllBooks(): array
    {
        return $this->createQueryBuilder('p')
            ->getQuery()
            ->getResult();
    }

    /**
     * Processes a book from the request and updates or creates a Book object.
     *
     * @param Request $request The request object containing book data.
     * @param Book|null $book The book object to update, or null to create a new one.
     * @return Book The updated or newly created Book object.
     */
    public function processBookFromRequest(Request $request, ?Book $book = null): Book
    {
        if (!$book) {
            $book = new Book();
        }
    
        $fields = [
            'title' => 'setTitle',
            'isbn' => 'setIsbn',
            'author' => 'setAuthor',
            'image_name' => 'setImg',
            'description' => 'setDescription',
            'year' => 'setPublishedYear'
        ];
    
        foreach ($fields as $requestField => $method) {
            $value = $request->request->get($requestField);
            if (!empty($value)) {
                $book->$method($value);
            }
        }
    
        return $book;
    }

    /**
     * Find a book by its ISBN.
     *
     * @param string $isbn The ISBN of the book to find.
     * @return Book|null The found book entity or null if no book was found.
     */
    public function findByISBN($isbn): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.isbn = :val')
            ->setParameter('val', $isbn)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Find all book IDs.
     *
     * @return array An array containing the IDs of all books.
     */
    public function findAllIds(): array
    {
        return $this->createQueryBuilder('b')
        ->select('b.id')
        ->getQuery()
        ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }
}
