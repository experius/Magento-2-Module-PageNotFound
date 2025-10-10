<?php

namespace Experius\PageNotFound\Observer\Controller;

use Exception;
use Experius\PageNotFound\Api\Data\PageNotFoundInterface;
use Experius\PageNotFound\Helper\Settings;
use Experius\PageNotFound\Model\PageNotFound;
use Experius\PageNotFound\Model\PageNotFoundFactory;
use Experius\PageNotFound\Model\PageNotFoundRepository;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\Cache\State;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

class ActionPredispatch implements ObserverInterface
{
    /**
     * @var array
     */
    protected array $urlParts = [];

    /**
     * @var Http
     */
    protected Http $request;

    /**
     * @var mixed
     */
    protected mixed $action;

    /**
     * @var PageNotFoundInterface|null
     */
    protected ?PageNotFoundInterface $pageNotFoundEntry = null;

    /**
     * @param UrlInterface $url
     * @param PageNotFoundFactory $pageNotFoundFactory
     * @param ResponseInterface $response
     * @param ActionFactory $actionFactory
     * @param State $cacheState
     * @param ScopeConfigInterface $scopeConfig
     * @param ResultFactory $resultFactory
     * @param StoreManagerInterface $storeManager
     * @param Settings $settings
     * @param PageNotFoundRepository $pageNotFoundRepository
     */
    public function __construct(
        protected UrlInterface           $url,
        protected PageNotFoundFactory    $pageNotFoundFactory,
        protected ResponseInterface      $response,
        protected ActionFactory          $actionFactory,
        protected State                  $cacheState,
        protected ScopeConfigInterface   $scopeConfig,
        protected ResultFactory          $resultFactory,
        protected StoreManagerInterface  $storeManager,
        protected Settings               $settings,
        protected PageNotFoundRepository $pageNotFoundRepository,

    )
    {
    }

    /**
     * @param $url
     * @return bool
     */
    protected function shouldExcludeUrl($url): bool
    {
        $excludeList = $this->settings->getExcludeList();
        if (empty($excludeList)) {
            return false;
        }

        $urlPath = parse_url($url, PHP_URL_PATH);
        $urlPath = ltrim($urlPath, '/');

        foreach ($excludeList as $excludeItem) {
            if (str_contains($urlPath, $excludeItem)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws Exception
     */
    public function execute(
        Observer $observer
    ): void
    {
        if (!$this->settings->isEnabled()) {
            return;
        }

        $this->request = $observer->getRequest();
        $this->action = $observer->getControllerAction();
        $type = 'full_page';
        $this->cacheState->setEnabled($type, false);
        $this->urlParts = parse_url($this->url->getCurrentUrl());
        $currentUrl = $this->getCurrentUrl();

        if (!$this->shouldExcludeUrl($currentUrl)) {
            $this->savePageNotFound($currentUrl);
        }

    }

    /**
     * @return Http
     */
    protected function getRequest(): Http
    {
        return $this->request;
    }

    /**
     * @return mixed
     */
    protected function getAction(): mixed
    {
        return $this->action;
    }

    /**
     * @return string
     */
    protected function getCurrentUrl(): string
    {
        return $this->stripFromUrl();
    }

    /**
     * @return string
     */
    protected function stripFromUrl(): string
    {
        $url_parts = $this->urlParts;

        // remove all params from url and add only the configured ones. <included_params>
        $params = (!empty($this->getParams(false))) ? '?' . $this->getParams(false) : '';
        return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . $params;
    }

    /**
     * @param $fromUrl
     * @param $isGraphql
     * @param StoreInterface|null $store
     * @return array|string|string[]|void
     * @throws Exception
     */
    protected function savePageNotFound($fromUrl, $isGraphql = false, ?StoreInterface $store = null)
    {
        $pageNotFoundModel = null;
        $baseUrl = $store?->getBaseUrl();

        // Create full url to return with GraphQL
        if ($isGraphql && !str_contains($fromUrl, $baseUrl)) {
            $fromUrl = $baseUrl . ltrim($fromUrl, '/');
        }

        try {
            $this->pageNotFoundEntry = $this->pageNotFoundRepository->getByFromUrl($fromUrl);
        } catch (NoSuchEntityException) {
            $pageNotFoundModel = $this->pageNotFoundFactory->create();
        }

        if ($pageNotFoundModel) {
            $currentDate = date("Y-m-d");
            $pageNotFoundModel->setLastVisited($currentDate);
            $pageNotFoundModel->setStoreId($this->getStoreId());

            if (!$pageNotFoundModel->getId()) {
                $pageNotFoundModel->setFromUrl($fromUrl);
                $pageNotFoundModel->setCount(1);
            }

            if ($pageNotFoundModel->getToUrl()) {
                $pageNotFoundModel->setCountRedirect($pageNotFoundModel->getCountRedirect() + 1);
            }

            $this->pageNotFoundRepository->save($pageNotFoundModel);
        }

        if ($this->pageNotFoundEntry) {
            if ($this->pageNotFoundEntry->getToUrl()) {
                return $this->redirect($this->pageNotFoundEntry->getToUrl(), $isGraphql, $baseUrl);
            }

            if (empty($this->pageNotFoundEntry->getToUrl())) {
                $count = $this->pageNotFoundEntry->getCount();
                $this->pageNotFoundEntry->setCount($count + 1);
            }
        }
    }

    /**
     * @param bool $redirect
     * @return string
     */
    protected function getParams(bool $redirect = true): string
    {
        $queryArray = $this->getRequest()->getParams();
        $unsetParams = ($redirect) ? $this->settings->includedParamsInRedirect() : $this->settings->includedParamsInFromUrl();

        foreach ($queryArray as $key => $value) {

            if (!in_array($key, $unsetParams) || !in_array(strtolower($key), $unsetParams)) {
                unset($queryArray[$key]);
            }
        }

        return http_build_query($queryArray);
    }

    /**
     * @param $url
     * @return bool
     */
    protected function urlHasParams($url): bool
    {
        $urlParts = parse_url($url);
        return isset($urlParts['query']) && $urlParts['query'];
    }

    /**
     * @param $url
     * @param $isGraphql
     * @param $baseUrl
     * @return array|string|string[]
     */
    protected function redirect($url, $isGraphql, $baseUrl): array|string
    {
        if ($url && $isGraphql) {
            return str_replace($baseUrl, '', $url);
        }

        if ($url === '410') {
            $result = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            return $result->setModule('experius_pagenotfound')->setController('response')->forward('gone');
        }

        // add all configured params to redirect url. <included_params_redirect>
        $queryStart = ($this->urlHasParams($url)) ? '&' : '?';
        $params = (!empty($this->getParams(true))) ? $queryStart . $this->getParams(true) : '';
        $url .= $params;

        header("HTTP/1.1 301 Moved Permanently");
        header("Location: " . $url);

        // Exit usage for problems with FPC
        // phpcs:disable
        exit();
        // phpcs:enable
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    protected function getStoreId(): int
    {
        return $this->storeManager->getStore()->getId() ?: 0;
    }
}
