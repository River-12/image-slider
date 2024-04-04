<?php

namespace Riverstone\ImageSlider\Block\Widget;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;
use Riverstone\ImageSlider\Helper\Data;
use Riverstone\ImageSlider\Helper\SliderImage;
use Riverstone\ImageSlider\Model\ResourceModel\Slider as ResourceModel;
use Riverstone\ImageSlider\Model\ResourceModel\Slider\CollectionFactory as SlidercollectionFactory;
use Riverstone\ImageSlider\Model\ResourceModel\SliderImage\CollectionFactory;
use Riverstone\ImageSlider\Model\SliderFactory;

class Slider extends Template implements BlockInterface
{
    protected $_template = "widget/slider.phtml";
    /**
     * Helper
     *
     * @var Data
     */
    protected $helperData;
    /**
     * Image Collection
     *
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * Image Slider
     *
     * @var SliderImage
     */
    protected $sliderImageHelper;
    /**
     * Image Collection Factory
     *
     * @var SliderCollectionFactory
     */
    protected $sliderCollectionFactory;
    /**
     * Assests
     *
     * @var Repository
     */
    protected $assetRepository;
    /**
     * Slider Resource Model
     *
     * @var ResourceModel
     */
    protected $resourceModel;
    /**
     * Image Factory
     *
     * @var SliderFactory
     */
    protected $modelFactory;

    /**
     * Constructor parameter
     *
     * @param Context $context
     * @param Data $helperData
     * @param CollectionFactory $collectionFactory
     * @param SliderImage $helper
     * @param SlidercollectionFactory $sliderCollectionFactory
     * @param Repository $assetRepository
     * @param ResourceModel $resourceModel
     * @param SliderFactory $modelFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helperData,
        CollectionFactory $collectionFactory,
        SliderImage $helper,
        SliderCollectionFactory $sliderCollectionFactory,
        Repository $assetRepository,
        ResourceModel $resourceModel,
        SliderFactory $modelFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperData = $helperData;
        $this->sliderImageHelper = $helper;
        $this->collectionFactory = $collectionFactory->create();
        $this->sliderCollectionFactory = $sliderCollectionFactory->create();
        $this->assetRepository = $assetRepository;
        $this->modelFactory = $modelFactory;
        $this->resourceModel = $resourceModel;
    }

    /**
     * Get Asset URL
     *
     * @param $asset
     * @return string
     */
    public function getAssetUrl($asset)
    {
        try {
            return $this->assetRepository->createAsset($asset)->getUrl();
        } catch (LocalizedException $e) {
            $this->_logger->critical($e->getMessage());
        }

        return "";
    }

    /**
     * Module Enable
     *
     * @return bool
     */
    public function isModuleEnable()
    {
        return $this->helperData->isModuleEnabled();
    }

    /**
     * Identifier
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->getSliderIdentifier();
    }

    /**
     * Slider Status
     *
     * @return mixed
     */
    public function getSliderStatus()
    {
        $sliderIdentifier = $this->getIdentifier();
        $sliderModel = $this->modelFactory->create();
        $this->resourceModel->load($sliderModel, $sliderIdentifier, "identifier");
        return $sliderModel->getStatus();
    }

    /**
     * Get Slider Images
     *
     * @return array
     */
    public function getSliderImages()
    {
        $sliderIdentifier = $this->getIdentifier();
        $sliderModel = $this->modelFactory->create();
        $this->resourceModel->load($sliderModel, $sliderIdentifier, "identifier");
        $sliderId = $sliderModel->getId();
        $sliderImages = $this->collectionFactory->addFieldToFilter('slider_id', ['eq' => $sliderId]);
        $imageUrls = [];
        foreach ($sliderImages as $sliderImage) {
            $imageUrl = $this->sliderImageHelper->getSliderImageUrl($sliderImage->getImagePath());
            $imageUrls[] = $imageUrl;
        }
        return $imageUrls;
    }
}
