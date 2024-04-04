<?php

namespace Riverstone\ImageSlider\Controller\Adminhtml\MassAction;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Riverstone\ImageSlider\Model\ResourceModel\Slider\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;

class MassDelete extends Action
{

    /**
     * New Filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * Slider Collection
     *
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Constructor params
     *
     * @param Context           $context
     * @param Filter            $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @return ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection(
            $this->collectionFactory->create()
        );
        $collectionSize = $collection->getSize();
        foreach ($collection as $item) {
            $item->delete();
        }
        $this->messageManager->addSuccessMessage(
            __(
                'A total of %1 Slider(s) have been deleted.',
                $collectionSize
            )
        );
        /**
         * @var Redirect $resultRedirect
         */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('slider/index/index');
    }
}
