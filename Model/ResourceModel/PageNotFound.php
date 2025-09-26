<?php


namespace Experius\PageNotFound\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;

class PageNotFound extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('experius_page_not_found', 'page_not_found_id');
        $this->addUniqueField(['field' => 'from_url', 'title' => __('A redirect for 404 Url already exists')]);
    }

    /**
     * @param array $ids
     * @throws LocalizedException
     */
    public function bulkDelete(array $ids)
    {
        $query = ['page_not_found_id IN (?)' => $ids];
        return $this->getConnection()->delete($this->getMainTable(), $query);
    }
}
