<?php

namespace Experius\PageNotFound\Observer\Controller;

class ActionPredispatch implements \Magento\Framework\Event\ObserverInterface
{
    protected $url;

    protected $pageNotFoundFactory;

    protected $response;

    protected $actionFactory;

    protected $scopeConfig;

    protected $cacheState;

    protected $request;

    protected $action;

    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Experius\PageNotFound\Model\PageNotFoundFactory $pageNotFoundFactory,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\Cache\State $cacheState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->url = $url;
        $this->pageNotFoundFactory = $pageNotFoundFactory;
        $this->response = $response;
        $this->actionFactory = $actionFactory;
        $this->urlParts = [];
        $this->cacheState = $cacheState;
        $this->scopeConfig = $scopeConfig;
    }

    private function isEnabled(){
        return $this->scopeConfig->getValue('pagenotfound/general/enabled',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    private function excludeParamsInFromUrl(){
        return $this->scopeConfig->getValue('pagenotfound/general/exclude_params_from_url',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    private function includeParamsInRedirect(){
        return $this->scopeConfig->getValue('pagenotfound/general/include_params_in_redirect',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    private function excludedParams(){
        return explode(',',$this->scopeConfig->getValue('pagenotfound/general/excluded_params',\Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

        if(!$this->isEnabled()){
            return;
        }

        $this->request = $observer->getRequest();
        $this->action = $observer->getControllerAction();

        foreach (['full_page'] as $type) {
            $this->cacheState->setEnabled($type, false);
        }

        $this->urlParts = parse_url($this->url->getCurrentUrl());

        $this->savePageNotFound($this->getCurrentUrl());

    }

    /* @return \Magento\Framework\App\RequestInterface */
    protected function getRequest(){
        return $this->request;
    }

    /* @return $action \Magento\Cms\Controller\Noroute\Index */
    protected function getAction(){
        return $this->action;
    }

    protected function getCurrentUrl(){
        return $this->stripFromUrl();
    }

    protected function stripFromUrl(){

        $url_parts = $this->urlParts;

        if($this->excludeParamsInFromUrl()) {
            $url = $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'];
        } else {
            $url = $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . '?' . $this->getParams();
        }

        return $url;
    }

    protected function savePageNotFound($fromUrl){

        /* @var $pageNotFoundModel \Experius\PageNotFound\Model\PageNotFound */
        $pageNotFoundModel = $this->pageNotFoundFactory->create();

        $pageNotFoundModel->load($fromUrl,'from_url');

        if($pageNotFoundModel->getId()){
            $count = $pageNotFoundModel->getCount();
            $pageNotFoundModel->setCount($count+1);
        } else {
            $pageNotFoundModel->setFromUrl($fromUrl);
            $pageNotFoundModel->setCount(1);
        }

        if($pageNotFoundModel->getToUrl()) {
            $pageNotFoundModel->setCountRedirect($pageNotFoundModel->getCountRedirect()+1);
        }

        $pageNotFoundModel->save();

        if($pageNotFoundModel->getToUrl()) {
            return $this->redirect($pageNotFoundModel->getToUrl(), '301');
        }
    }

    protected function getParams(){

        $queryArray = $this->getRequest()->getParams();

        foreach($queryArray as $key=>$value){
            if(in_array($key,$this->excludedParams())){
                unset($queryArray[$key]);
            }
        }
        return http_build_query($queryArray);
    }

    protected function redirect($url, $code)
    {
        if($this->includeParamsInRedirect() && isset($this->urlParts['query'])){
            $url = $url . '?' . $this->getParams();
        }

        $this->response->setRedirect($url,$code);
        $this->getRequest()->setDispatched(true);
        $this->getRequest()->setParam('no_cache', true);
        return $this->actionFactory->create('Magento\Framework\App\Action\Redirect');
    }
}
