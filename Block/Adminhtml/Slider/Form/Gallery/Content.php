<?php

namespace Riverstone\ImageSlider\Block\Adminhtml\Slider\Form\Gallery;

use Magento\Backend\Block\DataProviders\ImageUploadConfig as ImageUploadConfigDataProvider;
use Magento\Backend\Block\Media\Uploader;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\AbstractBlock;
use Riverstone\ImageSlider\Helper\SliderImage;
use Riverstone\ImageSlider\Model\ResourceModel\SliderImage\CollectionFactory as SliderImageCollectionFactory;

class Content extends Widget
{
    protected $_template = 'Riverstone_ImageSlider::slider/gallery.phtml';
    /**
     * Image Upload provider
     *
     * @var ImageUploadConfigDataProvider
     */
    private $imageUploadConfigDataProvider;
    /**
     * Http Request
     *
     * @var Http
     */
    protected $httpRequest;
    /**
     * Collection factory
     *
     * @var SliderImageCollectionFactory
     */
    protected $sliderImageCollectionFactory;
    /**
     * Slider Helper
     *
     * @var SliderImage
     */
    protected $sliderImageHelper;
    /**
     * JSON serializer
     *
     * @var Json
     */
    protected $serializer;
    protected $_mediaDirectory;

    /**
     * Constructor parameter
     *
     * @param Context $context
     * @param Http $httpRequest
     * @param SliderImageCollectionFactory $sliderImageCollectionFactory
     * @param SliderImage $sliderImageHelper
     * @param Json $serializer
     * @param Filesystem $filesystem
     * @param array $data
     * @param ImageUploadConfigDataProvider|null $imageUploadConfigDataProvider
     */
    public function __construct(
        Context $context,
        Http $httpRequest,
        SliderImageCollectionFactory $sliderImageCollectionFactory,
        SliderImage $sliderImageHelper,
        Json $serializer,
        Filesystem $filesystem,
        array $data = [],
        ImageUploadConfigDataProvider $imageUploadConfigDataProvider = null
    ) {
        parent::__construct($context, $data);
        $this->httpRequest = $httpRequest;
        $this->sliderImageCollectionFactory = $sliderImageCollectionFactory;
        $this->sliderImageHelper = $sliderImageHelper;
        $this->serializer = $serializer;
        $this->_filesystem = $filesystem;
        try {
            $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        } catch (FileSystemException $e) {
            $this->_logger->critical($e->getMessage());
        }
        $this->imageUploadConfigDataProvider = $imageUploadConfigDataProvider
            ?: ObjectManager::getInstance()->get(ImageUploadConfigDataProvider::class);
    }

    /**
     * Image Upload
     *
     * @return Content
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'uploader',
            Uploader::class,
            ['image_upload_config_data' => $this->imageUploadConfigDataProvider]
        );

        $this->getUploader()->getConfig()->setUrl(
            $this->_urlBuilder->getUrl('slider/image/upload')
        )->setFileField(
            'image'
        )->setFilters(
            [
                'images' => [
                    'label' => __('Images (.gif, .jpg, .png)'),
                    'files' => ['*.gif', '*.jpg', '*.jpeg', '*.png'],
                ],
            ]
        );

        return parent::_prepareLayout();
    }

    /**
     * return Images
     *
     * @return bool|string
     */
    public function getImagesJson()
    {
        $images = [];
        $sliderId = $this->httpRequest->getParam('id');
        $sliderImageCollection = $this->sliderImageCollectionFactory->create();
        $sliderImageCollection->addFieldToFilter('slider_id', $sliderId);

        foreach ($sliderImageCollection as $sliderImage) {
            $imagePath = $this->sliderImageHelper->getSliderImagePath() . $sliderImage->getImagePath();
            $imagePathExploded = explode("/", $sliderImage->getImagePath());
            $image['url'] = $this->sliderImageHelper->getSliderImageUrl($sliderImage->getImagePath());
            $image['image_id'] = $sliderImage->getId();
            $image['size'] = $this->_mediaDirectory->stat($imagePath)['size'];
            $image['name'] = end($imagePathExploded);

            $images[] = $image;
        }

        return $this->serializer->serialize($images);
    }

    /**
     * Upload
     *
     * @return string
     */
    public function getUploaderHtml()
    {
        return $this->getChildHtml('uploader');
    }

    /**
     * Child block upload
     *
     * @return bool|AbstractBlock
     */
    public function getUploader()
    {
        return $this->getChildBlock('uploader');
    }

    /**
     * Object
     *
     * @return string
     */
    public function getJsObjectName()
    {
        return $this->getHtmlId() . 'JsObject';
    }
}
