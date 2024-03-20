<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\PageNotFound\Cron;

use Experius\PageNotFound\Helper\UrlCleanUp;
use Experius\PageNotFound\Helper\Settings;
use Psr\Log\LoggerInterface;

class Clean
{
    /**
     * @param LoggerInterface $logger
     * @param UrlCleanUp $cleanHelper
     * @param Settings $settings
     */
    public function __construct(
        protected LoggerInterface $logger,
        protected UrlCleanUp $cleanHelper,
        protected Settings $settings
    ) {}

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
        $this->cleanHelper->execute();
    }
}
