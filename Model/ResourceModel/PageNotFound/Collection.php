<?php


namespace Experius\PageNotFound\Model\ResourceModel\PageNotFound;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Experius\PageNotFound\Model\PageNotFound',
            'Experius\PageNotFound\Model\ResourceModel\PageNotFound'
        );
    }
}
