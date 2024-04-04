<?php

namespace Riverstone\ImageSlider\Controller\Adminhtml\Image;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Riverstone\ImageSlider\Model\File\Uploader;
use Riverstone\ImageSlider\Helper\SliderImage;

class Upload extends Action
{
    protected $allowedMimeTypes = [
        'jpg' => 'image/jpg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/png',
        'png' => 'image/gif'
    ];
    /**
     * AdapterFactory config
     *
     * @var AdapterFactory
     */
    protected $adapterFactory;
    /**
     * SliderImage Helper
     *
     * @var SliderImage
     */
    protected $sliderImageHelper;
    /**
     * RawFactory result
     *
     * @var RawFactory
     */
    protected $resultRawFactory;
    /**
     * Json result
     *
     * @var Json
     */
    protected $json;

    /**
     * Constructor paramter
     *
     * @param Context $context
     * @param AdapterFactory $adapterFactory
     * @param SliderImage $sliderImageHelper
     * @param RawFactory $resultRawFactory
     * @param Json $json
     */
    public function __construct(
        Action\Context $context,
        AdapterFactory $adapterFactory,
        SliderImage $sliderImageHelper,
        RawFactory $resultRawFactory,
        Json $json
    ) {
        parent::__construct($context);
        $this->adapterFactory = $adapterFactory;
        $this->sliderImageHelper = $sliderImageHelper;
        $this->resultRawFactory = $resultRawFactory;
        $this->json = $json;
    }

    /**
     * Execute function
     *
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $uploader = $this->_objectManager->create(
                Uploader::class,
                ['fileId' => 'image']
            );
            $uploader->setAllowedExtensions($this->getAllowedExtensions());
            $imageAdapter = $this->adapterFactory->create();
            $uploader->addValidateCallback('slider_image', $imageAdapter, 'validateUploadFile');
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);

            $files = $this->getRequest()->getFiles('image');
            $explodedFileName = explode('.', $files['name']);
            $extension = end($explodedFileName);
            $newFileName = base64_encode(time()) . "." . $extension;

            $result = $uploader->save($this->sliderImageHelper->getSliderImageTmpPath(), $newFileName);

            unset($result['tmp_name']);
            unset($result['path']);

            $result['url'] = $this->sliderImageHelper->getSliderImageTmpUrl($result['file']);
            $result['temp_path'] = $result['file'];
        } catch (Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents($this->json->serialize($result));
        return $response;
    }

    /**
     * AllowedExtensions
     *
     * @return int[]|string[]
     */
    private function getAllowedExtensions()
    {
        return array_keys($this->allowedMimeTypes);
    }
}
