<?php

namespace Riverstone\ImageSlider\Controller\Adminhtml\Index;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Io\File;
use Riverstone\ImageSlider\Helper\SliderImage;
use Riverstone\ImageSlider\Model\ResourceModel\Slider;
use Riverstone\ImageSlider\Model\SliderFactory;
use Riverstone\ImageSlider\Model\SliderImageFactory;
use Riverstone\ImageSlider\Model\ResourceModel\SliderImage as SliderImageResource;

class Save extends Action
{
    /**
     * Create Slider factory
     *
     * @return SliderFactory
     */
    protected $sliderFactory;
    /**
     * Slider resource
     *
     * @return Slider
     */
    protected $resourceModel;
    /**
     * New Request
     *
     * @return RequestInterface
     */
    protected $request;
    /**
     * Set Data
     *
     * @return DataPersistorInterface
     */
    protected $dataPersistor;
    /**
     * New File
     *
     * @return File
     */
    protected $file;
    /**
     * Slider Helper
     *
     * @return SliderImage
     */
    protected $sliderImageHelper;
    /**
     * Resource
     *
     * @return SliderImageResource
     */
    protected $sliderImageResource;
    /**
     * Image factory
     *
     * @return SliderImageFactory
     */
    protected $sliderImageFactory;

    /**
     * Constructor params
     *
     * @param Context $context
     * @param SliderFactory $sliderFactory
     * @param DataPersistorInterface $dataPersistor
     * @param RequestInterface $request
     * @param Slider $resourceModel
     * @param File $file
     * @param SliderImage $sliderImageHelper
     * @param SliderImageResource $sliderImageResource
     * @param SliderImageFactory $sliderImageFactory
     */
    public function __construct(
        Context $context,
        SliderFactory $sliderFactory,
        DataPersistorInterface $dataPersistor,
        RequestInterface $request,
        Slider $resourceModel,
        File $file,
        SliderImage $sliderImageHelper,
        SliderImageResource $sliderImageResource,
        SliderImageFactory $sliderImageFactory
    ) {
        $this->resourceModel = $resourceModel;
        $this->sliderFactory = $sliderFactory;
        $this->request = $request;
        $this->dataPersistor = $dataPersistor;
        $this->file = $file;
        $this->sliderImageHelper = $sliderImageHelper;
        $this->sliderImageFactory = $sliderImageFactory;
        $this->sliderImageResource = $sliderImageResource;
        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return ResponseInterface|Redirect|ResultInterface|void
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $sliderData = $this->getRequest()->getPostValue();
        if (!empty($sliderData)) {
            if (empty($sliderData['id'])) {
                $sliderData['id'] = null;
            }
            $sliderModelFactory = $this->sliderFactory->create();
            $sliderModelFactory->setData($sliderData);

            try {
                $this->resourceModel->save($sliderModelFactory);

                if (isset($sliderData['slider']) && isset($sliderData['slider']['images'])) {
                    $this->saveImages($sliderData['slider']['images'], $sliderModelFactory->getId());
                }
                $this->messageManager->addSuccessMessage(__('You saved the Slider.'));
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['id' => $sliderModelFactory->getId(),
                            '_current' => true]
                    );
                }
                return $resultRedirect->setPath('*/*/index');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->dataPersistor->set('slider', $sliderData);
                return $resultRedirect->setPath('*/*/edit', ['id' => $sliderModelFactory->getId()]);
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('cannot save this Slider Try again'));
                return $resultRedirect->setPath('*/*/index');
            }
        }
    }

    /**
     * Save Images
     *
     * @param $images
     * @param $sliderId
     * @return void
     */
    public function saveImages($images, $sliderId)
    {
        foreach ($images as $image) {
            if (isset($image['is_removed']) && $image['is_removed']) {
                $sliderImage = $this->sliderImageFactory->create();
                try {
                    $this->sliderImageResource->load($sliderImage, $image['image_id']);
                    $this->sliderImageResource->delete($sliderImage);
                } catch (Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            } else {
                if (isset($image['is_new']) && ($image['is_new'] == "true")) {
                    $imageRelativeDir = implode("/", explode("/", $image['temp_path'], -1));
                    $imageAbsoluteDir = $this->sliderImageHelper->getSliderImagePath() . $imageRelativeDir;

                    $source = $this->sliderImageHelper->getSliderImageTmpPath() . $image['temp_path'];
                    $destination = $this->sliderImageHelper->getSliderImagePath() . $image['temp_path'];

                    if (!$this->file->fileExists($imageAbsoluteDir, false)) {
                        $this->file->mkdir($imageAbsoluteDir, 0775, true);
                    }

                    $this->file->mv($source, $destination);

                    $sliderImage = $this->sliderImageFactory->create();
                    $sliderImage->setSliderId($sliderId)->setImagePath($image['temp_path']);
                    try {
                        $this->sliderImageResource->save($sliderImage);
                    } catch (Exception $e) {
                        $this->messageManager->addErrorMessage($e->getMessage());
                    }
                }
            }
        }
    }
}
