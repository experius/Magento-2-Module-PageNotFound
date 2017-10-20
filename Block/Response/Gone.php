<?php


namespace Experius\PageNotFound\Block\Response;

class Gone extends \Magento\Framework\View\Element\Template
{

    protected function _prepareLayout()
    {
        $this->pageConfig->addBodyClass('410');
        $this->pageConfig->getTitle()->set('410 Gone');
        //$this->pageConfig->setKeywords();
        //$this->pageConfig->setDescription();

        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        $pageMainTitle->setPageTitle(__('Whoops, our bad...'));

        return parent::_prepareLayout();
    }

}