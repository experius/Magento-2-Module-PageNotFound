<?php


namespace Experius\PageNotFound\Controller\Adminhtml\Pagenotfound;

use /** @noinspection PhpUndefinedClassInspection */
    Experius\PageNotFound\Model\PageNotFoundFactory;
use Experius\PageNotFound\Model\PageNotFoundRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    /**
     * @var PageNotFoundRepository
     */
    protected $pageNotFoundRepository;

    /** @noinspection PhpUndefinedClassInspection */
    /**
     * @var PageNotFoundFactory
     */
    private $pageNotFoundFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        PageNotFoundRepository $pageNotFoundRepository,
        /** @noinspection PhpUndefinedClassInspection */
        PageNotFoundFactory $pageNotFoundFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->pageNotFoundRepository = $pageNotFoundRepository;
        $this->pageNotFoundFactory = $pageNotFoundFactory;
        return parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('page_not_found_id');

            try {
                $model = $this->pageNotFoundRepository->get($id);
            } catch (NoSuchEntityException $exception) {
                if ($id) {
                    $this->messageManager->addErrorMessage(__('This Page Not Found no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
                try {
                    $model = $this->pageNotFoundRepository->get($this->getRequest()->getParam('from_url'));
                    $this->messageManager->addWarningMessage(
                        __("This Page Not Found already existed so original one with id {$model->getId()} is updated.")
                    );
                } catch (NoSuchEntityException $exception) {
                    $model = $this->pageNotFoundFactory->create();
                }
            }

            $model->addData($data);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Page Not Found.'));
                $this->dataPersistor->clear('experius_pagenotfound_page_not_found');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['page_not_found_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the Page Not Found.: ' . $e->getMessage())
                );
            }
        
            $this->dataPersistor->set('experius_pagenotfound_page_not_found', $data);
            return $resultRedirect->setPath(
                '*/*/edit',
                ['page_not_found_id' => $this->getRequest()->getParam('page_not_found_id')]
            );
        }
        return $resultRedirect->setPath('*/*/');
    }
}
