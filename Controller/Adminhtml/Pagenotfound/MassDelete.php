<?php

namespace Experius\PageNotFound\Controller\Adminhtml\Pagenotfound;

use Experius\PageNotFound\Controller\Adminhtml\Pagenotfound;
use Experius\PageNotFound\Model\ResourceModel\PageNotFound\CollectionFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends Pagenotfound
{
    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        private readonly Context $context,
        private readonly Registry $coreRegistry,
        private readonly Filter $filter,
        private readonly CollectionFactory $collectionFactory,
    )
    {
        parent::__construct($context, $coreRegistry);
    }


    /**
     * Delete action
     *
     * @return ResultInterface
     * @throws LocalizedException
     */
    public function execute(): ResultInterface
    {
        // Find all entities that should be deleted
        $items = $this->collectionFactory->create();
        $collection = $this->filter->getCollection($items);

        // Delete them

        // Redirect to listing
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
