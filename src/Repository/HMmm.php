<?php

// namespace App\Repository;

// use App\Entity\Book;
// use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
// use Doctrine\Persistence\ManagerRegistry;

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
        $image_name = $request->request->get('image_name');

        $description = $request->request->get('description');
        $year = $request->request->get('year');
        var_dump($description);
        var_dump($year);
        var_dump($crash);
        


        if (!empty($title)) {
            $book->setTitle($title);
        }
        if (!empty($isbn)) {
            $book->setIsbn($isbn);
        }
        if (!empty($author)) {
            $book->setAuthor($author);
        }
        if (!empty($image_name)) {
            $book->setImg($image_name);
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
}
