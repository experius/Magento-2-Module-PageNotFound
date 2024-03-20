<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\PageNotFound\Cron;

use Experius\PageNotFound\Helper\UrlCleanUp;
use Experius\PageNotFound\Helper\Settings;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;


class Clean
{

    /**
     * @param UrlCleanUp $resourceConnection
     */
    public function __construct(
        protected LoggerInterface $logger,
        protected UrlCleanUp $cleanHelper,
        protected Settings $settings
    ) {
    }
    /**
     * Execute the cron
     * @return void
     */
    public function execute(): void
    {
        if(!$this->settings->getIsCronEnabled()){
            $this->logger->info(__("Cron is disabled for '404 reports'"));
            return;
        }
        if(!$this->cleanHelper->execute()){
            return;
        }
        $deletionCount = $this->cleanHelper->execute();
        return;
    }
}
