<?php


namespace Experius\PageNotFound\Model\ResourceModel;

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
    }
}
