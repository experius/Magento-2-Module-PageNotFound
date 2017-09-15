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

    /**
     * Get pagenotfound identifier by from_url
     *
     * @param string $fromUrl
     * @return int|false
     */
    public function getIdByFromUrl($fromUrl)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from($this->getMainTable(), 'page_not_found_id')
            ->where('from_url = :from_url');

        $bind = [':from_url' => (string)$fromUrl];

        return $connection->fetchOne($select, $bind);
    }

}
