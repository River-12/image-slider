<?php

namespace Riverstone\ImageSlider\Controller\Adminhtml\MassAction;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Riverstone\ImageSlider\Model\ResourceModel\Slider;
use Riverstone\ImageSlider\Model\ResourceModel\Slider\CollectionFactory;
use Riverstone\ImageSlider\Model\SliderFactory;

class MassStatus extends Action
{

    /**
     * New filter
     *
     * @var Filter
     */
    protected $filter;
    /**
     * Model Slider
     *
     * @var SliderFactory
     */
    protected $_model;
    /**
     * Resource Model
     *
     * @var Slider
     */
    protected $_resourceModel;
    /**
     * Slider Collection
     *
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Constructor Params
     *
     * @param Context $context
     * @param Filter $filter
     * @param SliderFactory $model
     * @param Slider $resourceModel
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        SliderFactory $model,
        Slider $resourceModel,
        CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_resourceModel = $resourceModel;
        $this->_model = $model;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return Redirect
     * @throws LocalizedException|Exception
     */
    public function execute()
    {
        $statusValue = $this->getRequest()->getParam('status');
        $slider = $this->_model->create();
        $collection = $this->filter->getCollection(
            $this->collectionFactory->create()
        );
        foreach ($collection as $item) {
            $sliderId = $item->getId();
            $this->_resourceModel->load($slider, $sliderId, "id");
            $slider->setStatus($statusValue);
            $this->_resourceModel->save($slider);
        }
        $this->messageManager->addSuccessMessage(
            __(
                'A total of %1 record(s) have been modified.',
                $collection->getSize()
            )
        );
        /**
         * @var Redirect $resultRedirect
         */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('slider/index/index');
    }
}
