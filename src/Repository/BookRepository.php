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

    public function processBookFromRequest(Request $request, ?Book $book = null): Book
    {
        if (!$book) {
            $book = new Book();
        }
        $title = $request->request->get('title');
        $isbn = $request->request->get('isbn');
        $author = $request->request->get('author');
        $img = $request->request->get('image_name');
        $description = $request->request->get('description');
        $year = $request->request->get('year');

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
            $book->setDescription($description);
        }
        if (!empty($year)) {
            $book->setPublishedYear($year);
        }

        return $book;
    }


    public function findByISBN($isbn): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.isbn = :val')
            ->setParameter('val', $isbn)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findAllIds(): array
    {
        return $this->createQueryBuilder('b')
        ->select('b.id')
        ->getQuery()
        ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }
}
