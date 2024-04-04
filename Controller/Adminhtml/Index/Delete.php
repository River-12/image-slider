<?php

namespace Riverstone\ImageSlider\Controller\Adminhtml\Index;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Riverstone\ImageSlider\Model\ResourceModel\Slider as ResourceModel;
use Riverstone\ImageSlider\Model\SliderFactory;

class Delete extends Action
{
    /**
     * Model factory
     *
     * @var SliderFactory
     */
    protected $_model;
    /**
     * Resource Factory
     *
     * @var ResourceModel
     */
    protected $_resourceModel;

    /**
     * Constructor Parameter
     *
     * @param Action\Context $context
     * @param SliderFactory $model
     * @param ResourceModel $resourceModel
     */
    public function __construct(
        Action\Context $context,
        SliderFactory $model,
        ResourceModel $resourceModel
    ) {
        parent::__construct($context);
        $this->_model = $model;
        $this->_resourceModel = $resourceModel;
    }

    /**
     * Delete action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->_model->create();
                $this->_resourceModel->load($model, $id, "id");
                $this->_resourceModel->delete($model);
                $this->messageManager->addSuccessMessage(__('Slider Deleted'));
                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('Slider does not exist'));
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Allowed Function
     *
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Riverstone_ImageSlider::menu');
    }
}
