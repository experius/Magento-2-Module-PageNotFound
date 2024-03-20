<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\PageNotFound\Helper;

use Experius\PageNotFound\Helper\Settings;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

class UrlCleanUp
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @param LoggerInterface $logger
     * @param ResourceConnection $resourceConnection
     * @param Settings $settings
     */
    public function __construct(
        protected LoggerInterface $logger,
        protected ResourceConnection $resourceConnection,
        private Settings $settings
    ) {
        $this->connection = $this->resourceConnection->getConnection();
    }


    /**
     * Get days to clean
     * @return mixed
     */
    public function getDaysToClean($days = null): mixed
    {
        if($days){
            return (int)$days;
        }
        $daysToCleanConfig = $this->settings->getConfigDaysToClean();

        if($daysToCleanConfig){
            $this->logger->info(__("clearing everything older then %1 days", $daysToCleanConfig));
            return (int)$daysToCleanConfig;
        }

        $this->logger->info(__("no days set in stores -> Configuration -> advanced -> 404 reports -> config"));

        return false;
    }


    /**
     * build querry
     * @return mixed
     */
    public function addEmptyRedirectQuerry($where)
    {
        $getDeleteNotEmpyRedirect = $this->settings->getDeleteNotEmpyRedirect();
        if(!$getDeleteNotEmpyRedirect){
            $where .= " AND to_url is null";
            return $where;
        }

        $where .= " AND to_url is not null";
        return $where;
    }


    /**
     * execute cleanup
     * @return int
     */
    public function execute($days = null)//: int
    {
        $getDeleteNotEmpyRedirect=$this->settings->getDeleteNotEmpyRedirect();

        if(!$this->getDaysToClean($days))
        {
            return 0;
        }
        $where = "last_visited < '" . date('c', time() - ($this->getDaysToClean($days) * (3600 * 24))) . "'";

        if(!$days) {
            $where = $this->addEmptyRedirectQuerry($where);
        } else {
            $where .= " AND to_url is null";
        }

        $deletionCount = $this->connection->delete(
            $this->resourceConnection->getTableName('experius_page_not_found'),
            $where
        );

        $this->logger->info(__('Experius 404 url Cleanup: Removed %1 records.', $deletionCount));

        return $deletionCount;
    }

}
