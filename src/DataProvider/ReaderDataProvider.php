<?php
namespace App\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Api\Config;
use App\Entity\Library\Reader as OrmEntityReader;
use Doctrine\Common\Persistence\ManagerRegistry;

class ReaderDataProvider implements ItemDataProviderInterface, CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @var Config
     */
    protected $apiPlatformConfig = [];

    /**
     * @var iterable
     */
    protected $itemExtensions = [];

    /**
     * @var iterable
     */
    protected $collectionExtensions = [];

    public function __construct(ManagerRegistry $managerRegistry, Config $apiPlatformConfig, iterable $itemExtensions, iterable $collectionExtensions)
    {
        $this->managerRegistry = $managerRegistry;
        $this->apiPlatformConfig = $apiPlatformConfig;
        $this->itemExtensions = $itemExtensions;
        $this->collectionExtensions = $collectionExtensions;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return OrmEntityReader::class === $resourceClass;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $em = $this->managerRegistry->getRepository(OrmEntityReader::class);

        /**
         * @todo find a way to retreive only props specified in uri if they exists => Extensions might have helped us but they works only if entity are both orm & apiPlatform
         */

        /*$books = new ArrayCollection();
        foreach ($item->getBooks() as $book) {
            $bb = new Book();
            $bb->setId($book->getId());
            $books[] = $bb;
        }

        $item->setBooks($books);*/

        return $em->find($id);
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $items = [];
        $em = $this->managerRegistry->getRepository(OrmEntityReader::class);
        $qb = $em->createQueryBuilder('r');
        $queryNameGenerator = new QueryNameGenerator();

        /**
         * @todo manage extensions sort, search, pagination, at least
         */
        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection($qb, $queryNameGenerator, $resourceClass, $operationName, $context);
            if ($extension instanceof QueryResultCollectionExtensionInterface
                && $extension->supportsResult($resourceClass, $operationName, $context)) {
                $items = $extension->getResult($qb, $resourceClass, $operationName, $context);
            }
        }

        return $items;
    }

}