<?php


namespace Experius\PageNotFound\Model;

use Experius\PageNotFound\Api\Data\PageNotFoundInterface;

class PageNotFound extends \Magento\Framework\Model\AbstractModel implements PageNotFoundInterface
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Experius\PageNotFound\Model\ResourceModel\PageNotFound');
    }

    /**
     * Get page_not_found_id
     * @return string
     */
    public function getPageNotFoundId()
    {
        return $this->getData(self::PAGE_NOT_FOUND_ID);
    }

    /**
     * Set page_not_found_id
     * @param string $pageNotFoundId
     * @return \Experius\PageNotFound\Api\Data\PageNotFoundInterface
     */
    public function setPageNotFoundId($pageNotFoundId)
    {
        return $this->setData(self::PAGE_NOT_FOUND_ID, $pageNotFoundId);
    }

    /**
     * Get from_url
     * @return string
     */
    public function getFromUrl()
    {
        return $this->getData(self::FROM_URL);
    }

    /**
     * Set from_url
     * @param string $from_url
     * @return \Experius\PageNotFound\Api\Data\PageNotFoundInterface
     */
    public function setFromUrl($from_url)
    {
        return $this->setData(self::FROM_URL, $from_url);
    }

    /**
     * Get to_url
     * @return string
     */
    public function getToUrl()
    {
        return $this->getData(self::TO_URL);
    }

    /**
     * Set to_url
     * @param string $to_url
     * @return \Experius\PageNotFound\Api\Data\PageNotFoundInterface
     */
    public function setToUrl($to_url)
    {
        return $this->setData(self::TO_URL, $to_url);
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->getData(self::COUNT);
    }

    /**
     * Set count
     * @param int $count
     * @return \Experius\PageNotFound\Api\Data\PageNotFoundInterface
     */
    public function setCount($count)
    {
        return $this->setData(self::COUNT, $count);
    }

    /**
     * Get count_redirect
     * @return int
     */
    public function getCountRedirect()
    {
        return $this->getData(self::COUNT_REDIRECT);
    }

    /**
     * Set count_redirect
     * @param int $count_redirect
     * @return \Experius\PageNotFound\Api\Data\PageNotFoundInterface
     */
    public function setCountRedirect($count_redirect)
    {
        return $this->setData(self::COUNT_REDIRECT, $count_redirect);
    }
}
