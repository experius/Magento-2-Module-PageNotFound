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

    const TABLE = 'experius_page_not_found';

    /**
     * @param LoggerInterface $logger
     * @param ResourceConnection $resourceConnection
     * @param Settings $settings
     */
    public function __construct(
        protected LoggerInterface    $logger,
        protected ResourceConnection $resourceConnection,
        private Settings             $settings
    )
    {
        $this->connection = $this->resourceConnection->getConnection();
    }

    /**
     * Get days to clean
     * @return int
     */
    public function getDaysToClean($days = null): int
    {
        if ($days) {
            return (int)$days;
        }

        return (int)$this->settings->getConfigDaysToClean();
    }

    /**
     * execute cleanup
     * @return int
     */
    public function execute($days = null): int
    {
        $where = ("last_visited < '" . date('c', time() - ($this->getDaysToClean($days) * (3600 * 24))) . "'");

        if(!$this->settings->getDeleteNotEmpyRedirect())
            $where .= (' AND to_url IS NULL');

        $deletionCount = $this->connection->delete(self::TABLE, $where);

        $this->logger->info(__('Experius 404 url Cleanup: Removed %1 records.', $deletionCount));

        return $deletionCount;
    }

}
