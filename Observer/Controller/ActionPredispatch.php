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
    
    protected $urlParts = [];

    private $resultFactory;

    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Experius\PageNotFound\Model\PageNotFoundFactory $pageNotFoundFactory,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\Cache\State $cacheState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Controller\ResultFactory $resultFactory
    ) {
        $this->url = $url;
        $this->pageNotFoundFactory = $pageNotFoundFactory;
        $this->response = $response;
        $this->actionFactory = $actionFactory;
        $this->cacheState = $cacheState;
        $this->scopeConfig = $scopeConfig;
        $this->resultFactory = $resultFactory;
    }

    private function isEnabled(){
        return $this->scopeConfig->getValue('pagenotfound/general/enabled',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    private function includedParamsInRedirect(){
        return explode(',',$this->scopeConfig->getValue('pagenotfound/general/included_params_redirect',\Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }

    private function includedParamsInFromUrl(){
        return explode(',',$this->scopeConfig->getValue('pagenotfound/general/included_params_from_url',\Magento\Store\Model\ScopeInterface::SCOPE_STORE));
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

    /* @return \Magento\Cms\Controller\Noroute\Index */
    protected function getAction(){
        return $this->action;
    }

    protected function getCurrentUrl(){
        return $this->stripFromUrl();
    }

    protected function stripFromUrl(){

        $url_parts = $this->urlParts;

        // remove all params from url and add only the configured ones. <included_params>
        $params = (!empty($this->getParams(false))) ? '?' . $this->getParams(false) : '';
        $url = $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . $params;

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

     /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function getParams($redirect=true){

        $queryArray = $this->getRequest()->getParams();

        $unsetParams = ($redirect) ? $this->includedParamsInRedirect() : $this->includedParamsInFromUrl();

        foreach($queryArray as $key=>$value){

            if(!in_array($key,$unsetParams) || !in_array(strtolower($key),$unsetParams)){
                unset($queryArray[$key]);
            }

        }

        return http_build_query($queryArray);
    }

    protected function urlHasParams($url){
        $urlParts = parse_url($url);
        if(isset($urlParts['query']) && $urlParts['query']){
            return true;
        }
        return false;
    }

    /**
     * @param $url
     * @return \Magento\Framework\App\ActionInterface
     * @SuppressWarnings(PHPMD)
     */
    protected function redirect($url)
    {

        if($url=='410'){
            $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_FORWARD);
            return $result->setModule('experius_pagenotfound')->setController('response')->forward('gone');
        } else {
            // add all configured params to redirect url. <included_params_redirect>
            $queryStart = ($this->urlHasParams($url)) ? '&' : '?';
            $params = (!empty($this->getParams(true))) ? $queryStart . $this->getParams(true) : '';
            $url = $url . $params;
            header("HTTP/1.1 301 Moved Permanently");
        }

        header("Location: " . $url);
        // Exit usage for problems with FPC
        // phpcs:disable
        exit();
        // phpcs:enable

        $this->response->setRedirect($url,$code);

        $this->getRequest()->setDispatched(true);
        $this->getRequest()->setParam('no_cache', true);

        return $this->actionFactory->create('Magento\Framework\App\Action\Redirect');
    }
}
