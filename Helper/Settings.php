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

    const IS_CRON_ENABLED = 'pagenotfound/cron_config/is_cron_enabled';
    const CONFIG_DAYS_TO_CLEAN = 'pagenotfound/cron_config/days_to_clean';
    const DELETE_NOT_EMPTY_REDIRECT = 'pagenotfound/cron_config/delete_not_empty_redirect';
    const ENABLED = 'pagenotfound/general/enabled';
    const INCLUDED_PARAMS_REDIRECT = 'pagenotfound/general/included_params_redirect';
    const INCLUDED_PARAMS_FROM_URL = 'pagenotfound/general/included_params_from_url';
    const EXCLUDE_LIST = 'pagenotfound/general/exclude_list';
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
     * @return int
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

    public function isEnabled()
    {
        return $this->scopeConfig->getValue(self::ENABLED,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function includedParamsInRedirect()
    {
        $configValue = $this->scopeConfig->getValue(self::INCLUDED_PARAMS_REDIRECT,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $configValue ? explode(',',$configValue) : [];
    }

    public function includedParamsInFromUrl()
    {
        $configValue = $this->scopeConfig->getValue(self::INCLUDED_PARAMS_FROM_URL,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $configValue ? explode(',',$configValue) : [];

    }

    public function getExcludeList()
    {
        $excludeList = $this->scopeConfig->getValue(self::EXCLUDE_LIST,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $excludeList = explode(',', $excludeList);
        return $excludeList;
    }
}
