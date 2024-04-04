<?php

namespace Riverstone\ImageSlider\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Riverstone\ImageSlider\Model\ResourceModel\Slider;
use Riverstone\ImageSlider\Model\SliderFactory;

class Edit extends Action
{
    /**
     * PageFactory config
     *
     * @var PageFactory
     */
    protected $resultPage;
    protected $resultPageFactory = false;
    /**
     * Registry value
     *
     * @var Registry
     */
    protected $coreRegistry;
    /**
     * SliderModel Factory
     *
     * @var SliderFactory
     */
    protected $model;
    /**
     * Slider ResourceModel
     *
     * @var Slider
     */
    protected $resourceModel;

    /**
     * Constructor parameter
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $coreRegistry
     * @param SliderFactory $model
     * @param Slider $resourceModel
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $coreRegistry,
        SliderFactory $model,
        Slider $resourceModel
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->model = $model;
        $this->resourceModel = $resourceModel;
    }

    /**
     * Execute function
     *
     * @return ResponseInterface|ResultInterface|Page|PageFactory
     */
    public function execute()
    {
        $sliderId = $this->getRequest()->getParam('id');
        $slider = $this->model->create();
        if ($sliderId) {
            $this->resourceModel->load($slider, $sliderId, "id");
            if ($slider->getId()) {
                $this->coreRegistry->register('slider', $slider);
            }
        }
        $resultPage = $this->getResultPage();
        $resultPage->getConfig()->getTitle()->prepend(
            $slider->getId() ? $slider->getName() : __('New Slider')
        );
        return $resultPage;
    }

    /**
     * Result page
     *
     * @return Page|PageFactory
     */
    public function getResultPage()
    {
        $this->resultPage = $this->resultPageFactory->create();
        return $this->resultPage;
    }
}
