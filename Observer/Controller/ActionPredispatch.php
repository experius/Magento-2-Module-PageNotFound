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
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

        /* @var $request \Magento\Framework\App\RequestInterface */
        $request = $observer->getRequest();

        /* @var $action \Magento\Cms\Controller\Noroute\Index */
        $action = $observer->getControllerAction();

        $this->savePageNotFound($this->getCurrentUrl(),$request);

    }

    protected function getCurrentUrl(){
        return $this->stripUrl($this->url->getCurrentUrl());
    }

    protected function stripUrl($url){

        $excludeParams = [];

        $urlSplit = explode('?',$url);
        $urlParams = explode('&',end($urlSplit));

        $url = reset($urlSplit);

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
        $this->response->setRedirect($url,$code);
        $request->setDispatched(true);
        return $this->actionFactory->create('Magento\Framework\App\Action\Redirect');
    }
}
