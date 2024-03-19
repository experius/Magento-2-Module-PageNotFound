<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\PageNotFound\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class Settings extends AbstractHelper
{

    const IS_CRON_ENABLED = 'page_not_found/config/is_cron_enabled';
    const CONFIG_DAYS_TO_CLEAN = 'page_not_found/config/days_to_clean';
    const DELETE_NOT_EMPTY_REDIRECT = 'page_not_found/config/delete_not_empty_redirect';

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
    }
    /**
     * @return bool
     */
    public function getIsCronEnabled()
    {
        return $this->scopeConfig->getValue(self::IS_CRON_ENABLED);
    }

    /**
     * @return integer
     */
    public function getConfigDaysToClean()
    {
        return $this->scopeConfig->getValue(self::CONFIG_DAYS_TO_CLEAN);
    }

    /**
     * @return string
     */
    public function getDeleteNotEmpyRedirect()
    {
        return $this->scopeConfig->getValue(self::DELETE_NOT_EMPTY_REDIRECT);
    }

}
