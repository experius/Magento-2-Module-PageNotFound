<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\PageNotFound\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;

class Settings extends AbstractHelper
{

    const IS_CRON_ENABLED = 'pagenotfound/config/is_cron_enabled';
    const CONFIG_DAYS_TO_CLEAN = 'pagenotfound/config/days_to_clean';
    const DELETE_NOT_EMPTY_REDIRECT = 'pagenotfound/config/delete_not_empty_redirect';

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
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
