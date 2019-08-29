<?php


namespace Experius\PageNotFound\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\DataObjectHelper;
use Experius\PageNotFound\Api\Data\PageNotFoundInterfaceFactory;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SortOrder;
use Experius\PageNotFound\Api\PageNotFoundRepositoryInterface;
use Experius\PageNotFound\Api\Data\PageNotFoundSearchResultsInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Experius\PageNotFound\Model\ResourceModel\PageNotFound as ResourcePageNotFound;
use Experius\PageNotFound\Model\ResourceModel\PageNotFound\CollectionFactory as PageNotFoundCollectionFactory;

class PageNotFoundRepository implements PageNotFoundRepositoryInterface
{

    private $storeManager;

    protected $pageNotFoundCollectionFactory;

    protected $pageNotFoundFactory;

    protected $dataObjectProcessor;

    protected $dataPageNotFoundFactory;

    protected $searchResultsFactory;

    protected $resource;

    protected $dataObjectHelper;


    /**
     * @param ResourcePageNotFound $resource
     * @param PageNotFoundFactory $pageNotFoundFactory
     * @param PageNotFoundInterfaceFactory $dataPageNotFoundFactory
     * @param PageNotFoundCollectionFactory $pageNotFoundCollectionFactory
     * @param PageNotFoundSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourcePageNotFound $resource,
        PageNotFoundFactory $pageNotFoundFactory,
        PageNotFoundInterfaceFactory $dataPageNotFoundFactory,
        PageNotFoundCollectionFactory $pageNotFoundCollectionFactory,
        PageNotFoundSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->pageNotFoundFactory = $pageNotFoundFactory;
        $this->pageNotFoundCollectionFactory = $pageNotFoundCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPageNotFoundFactory = $dataPageNotFoundFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Experius\PageNotFound\Api\Data\PageNotFoundInterface $pageNotFound
    ) {
        /* if (empty($pageNotFound->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $pageNotFound->setStoreId($storeId);
        } */
        try {
            $pageNotFound->getResource()->save($pageNotFound);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the pageNotFound: %1',
                $exception->getMessage()
            ));
        }
        return $pageNotFound;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($pageNotFoundId)
    {
        $pageNotFound = $this->pageNotFoundFactory->create();
        $pageNotFound->getResource()->load($pageNotFound, $pageNotFoundId);
        if (!$pageNotFound->getId()) {
            throw new NoSuchEntityException(__('page_not_found with id "%1" does not exist.', $pageNotFoundId));
        }
        return $pageNotFound;
    }

    /**
     * {@inheritdoc}
     */
    public function getByFromUrl($pageNotFoundUrl)
    {
        $pageNotFound = $this->pageNotFoundFactory->create();
        $pageNotFound->getResource()->load($pageNotFound, $pageNotFoundUrl, 'from_url');
        if (!$pageNotFound->getId()) {
            throw new NoSuchEntityException(__('page_not_found with id "%1" does not exist.', $pageNotFound->getId()));
        }
        return $pageNotFound;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->pageNotFoundCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                if (!$sortOrder->getField()) {
                    continue;
                }
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Experius\PageNotFound\Api\Data\PageNotFoundInterface $pageNotFound
    ) {
        try {
            $pageNotFound->getResource()->delete($pageNotFound);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the page_not_found: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($pageNotFoundId)
    {
        return $this->delete($this->getById($pageNotFoundId));
    }
}
