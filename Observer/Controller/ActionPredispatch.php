<?php

namespace Experius\PageNotFound\Observer\Controller;

class ActionPredispatch implements \Magento\Framework\Event\ObserverInterface
{
    protected $url;

    protected $pageNotFoundFactory;

    protected $response;

    protected $actionFactory;

    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Experius\PageNotFound\Model\PageNotFoundFactory $pageNotFoundFactory,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\App\ActionFactory $actionFactory
    ) {
        $this->url = $url;
        $this->pageNotFoundFactory = $pageNotFoundFactory;
        $this->response = $response;
        $this->actionFactory = $actionFactory;
        $this->urlParts = [];
    }

    private function excludeParamsInFromUrl(){
        return true;
    }

    private function includeParamsInRedirect(){
        return true;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

        /* @var $request \Magento\Framework\App\RequestInterface */
        $request = $observer->getRequest();

        $this->urlParts = parse_url($this->url->getCurrentUrl());

        /* @var $action \Magento\Cms\Controller\Noroute\Index */
        $action = $observer->getControllerAction();

        $this->savePageNotFound($this->getCurrentUrl(),$request);

    }

    protected function getCurrentUrl(){
        return $this->stripUrl();
    }

    protected function stripUrl(){

        $excludeParams = [];

        $url_parts = $this->urlParts;

        $url = $this->url->getCurrentUrl();

        if($this->excludeParamsInFromUrl()) {
            $url = $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'];
        }

        return $url;
    }

    protected function savePageNotFound($fromUrl,$request){

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
            return $this->redirect($request, $pageNotFoundModel->getToUrl(), '301');
        }
    }

    protected function redirect($request, $url, $code)
    {

        if($this->includeParamsInRedirect() && isset($this->urlParts['query'])){
            $url = $url . '?' . $this->urlParts['query'];
        }

        header("HTTP/1.1 301 Moved Permanently");
        header("Location: " . $url);
        exit();

        // TODO make this work with fpc.
        //$this->response->setRedirect($url,$code);
        //$request->setDispatched(true);
        //return $this->actionFactory->create('Magento\Framework\App\Action\Redirect');
    }
}
