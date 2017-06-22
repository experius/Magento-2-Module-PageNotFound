<?php


namespace Experius\PageNotFound\Api\Data;

interface PageNotFoundSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get page_not_found list.
     * @return \Experius\PageNotFound\Api\Data\PageNotFoundInterface[]
     */
    
    public function getItems();

    /**
     * Set from_url list.
     * @param \Experius\PageNotFound\Api\Data\PageNotFoundInterface[] $items
     * @return $this
     */
    
    public function setItems(array $items);
}
