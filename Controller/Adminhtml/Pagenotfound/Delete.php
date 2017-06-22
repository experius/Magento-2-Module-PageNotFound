<?php


namespace Experius\PageNotFound\Controller\Adminhtml\Pagenotfound;

class Delete extends \Experius\PageNotFound\Controller\Adminhtml\Pagenotfound
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('page_not_found_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Experius\PageNotFound\Model\PageNotFound');
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Page Not Found.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['page_not_found_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Page Not Found to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
