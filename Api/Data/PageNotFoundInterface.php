<?php


namespace Experius\PageNotFound\Api\Data;

interface PageNotFoundInterface
{

    const TO_URL = 'to_url';
    const COUNT_REDIRECT = 'count_redirect';
    const LAST_VISITED = 'last_visited';
    const COUNT = 'count';
    const FROM_URL = 'from_url';
    const PAGE_NOT_FOUND_ID = 'page_not_found_id';


    /**
     * Get page_not_found_id
     * @return string|null
     */

    public function getPageNotFoundId();

    /**
     * Set page_not_found_id
     * @param string $page_not_found_id
     * @return \Experius\PageNotFound\Api\Data\PageNotFoundInterface
     */

    public function setPageNotFoundId($pageNotFoundId);

    /**
     * Get from_url
     * @return string|null
     */

    public function getFromUrl();

    /**
     * Set from_url
     * @param string $from_url
     * @return \Experius\PageNotFound\Api\Data\PageNotFoundInterface
     */

    public function setFromUrl($from_url);

    /**
     * Get to_url
     * @return string|null
     */

    public function getToUrl();

    /**
     * Set to_url
     * @param string $to_url
     * @return \Experius\PageNotFound\Api\Data\PageNotFoundInterface
     */

    public function setToUrl($to_url);

    /**
     * Get count
     * @return string|null
     */

    public function getCount();

    /**
     * Set count
     * @param string $count
     * @return \Experius\PageNotFound\Api\Data\PageNotFoundInterface
     */

    public function setCount($count);

    /**
     * Get count_redirect
     * @return string|null
     */

    public function getCountRedirect();

    /**
     * Set count_redirect
     * @param string $count_redirect
     * @return \Experius\PageNotFound\Api\Data\PageNotFoundInterface
     */

    public function setCountRedirect($count_redirect);

    /**
     * Get last_visited
     * @return string|null
     */
    public function getLastVisited();

    /**
     * Set last_visited
     * @param string $last_visited
     * @return \Experius\PageNotFound\Api\Data\PageNotFoundInterface
     */
    public function setLastVisited($last_visited);
}
