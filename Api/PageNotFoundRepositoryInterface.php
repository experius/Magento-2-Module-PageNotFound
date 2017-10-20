<?php


namespace Experius\PageNotFound\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface PageNotFoundRepositoryInterface
{


    /**
     * Save page_not_found
     * @param \Experius\PageNotFound\Api\Data\PageNotFoundInterface $pageNotFound
     * @return \Experius\PageNotFound\Api\Data\PageNotFoundInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function save(
        \Experius\PageNotFound\Api\Data\PageNotFoundInterface $pageNotFound
    );

    /**
     * Retrieve page_not_found
     * @param string $pageNotFoundId
     * @return \Experius\PageNotFound\Api\Data\PageNotFoundInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function getById($pageNotFoundId);

    /**
     * Retrieve page_not_found
     * @param string $pageNotFoundUrl
     * @return \Experius\PageNotFound\Api\Data\PageNotFoundInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getByFromUrl($pageNotFoundUrl);

    /**
     * Retrieve page_not_found matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Experius\PageNotFound\Api\Data\PageNotFoundSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete page_not_found
     * @param \Experius\PageNotFound\Api\Data\PageNotFoundInterface $pageNotFound
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function delete(
        \Experius\PageNotFound\Api\Data\PageNotFoundInterface $pageNotFound
    );

    /**
     * Delete page_not_found by ID
     * @param string $pageNotFoundId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function deleteById($pageNotFoundId);
}
