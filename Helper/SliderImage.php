<?php

namespace Riverstone\ImageSlider\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class SliderImage
{
    // phpcs:ignore
    const SLIDER_IMAGE_RELATIVE_PATH = "riverstone" . DIRECTORY_SEPARATOR . "slider" . DIRECTORY_SEPARATOR
    . "images";
    // phpcs:ignore
    const SLIDER_IMAGE_TMP_RELATIVE_PATH = self::SLIDER_IMAGE_RELATIVE_PATH . DIRECTORY_SEPARATOR . "tmp";
    /**
     * StoreManager config
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * DirectoryList value
     *
     * @var DirectoryList
     */
    protected $directorylist;
    /**
     * LoggerInterface
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor parameter
     *
     * @param StoreManagerInterface $storeManager
     * @param DirectoryList $directoryList
     * @param LoggerInterface $logger
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        DirectoryList $directoryList,
        LoggerInterface $logger
    ) {
        $this->storeManager = $storeManager;
        $this->directorylist = $directoryList;
        $this->logger = $logger;
    }

    /**
     * Slider Imagepath
     *
     * @return string
     */
    public function getSliderImagePath()
    {
        try {
            return $this->directorylist->getPath(DirectoryList::MEDIA)
                . DIRECTORY_SEPARATOR
                . self::SLIDER_IMAGE_RELATIVE_PATH;
        } catch (FileSystemException $e) {
            $this->logger->critical($e->getMessage());
        }

        return "";
    }

    /**
     * SliderImageTmpPath
     *
     * @return string
     */
    public function getSliderImageTmpPath()
    {
        try {
            return $this->directorylist->getPath(DirectoryList::MEDIA)
                . DIRECTORY_SEPARATOR
                . self::SLIDER_IMAGE_TMP_RELATIVE_PATH;
        } catch (FileSystemException $e) {
            $this->logger->critical($e->getMessage());
        }

        return "";
    }
    /**
     * SliderImageTmpUrl
     *
     * @param $file
     * @return string
     */
    public function getSliderImageTmpUrl($file)
    {
        $mediaUrl = $this->getMediaUrl();
        return $mediaUrl . self::SLIDER_IMAGE_TMP_RELATIVE_PATH . DIRECTORY_SEPARATOR . $this->prepareFile($file);
    }

    /**
     * SliderImageUrl
     *
     * @param $file
     * @return string
     */
    public function getSliderImageUrl($file)
    {
        $mediaUrl = $this->getMediaUrl();
        return $mediaUrl . self::SLIDER_IMAGE_RELATIVE_PATH . DIRECTORY_SEPARATOR . $this->prepareFile($file);
    }

    /**
     * MediaUrl
     *
     * @return string
     */
    protected function getMediaUrl()
    {
        try {
            return $this->storeManager->getStore()->getBaseUrl(
                UrlInterface::URL_TYPE_MEDIA
            );
        } catch (NoSuchEntityException $e) {
            $this->logger->critical($e->getMessage());
        }

        return "";
    }

    /**
     * PrepareFile
     *
     * @param $file
     * @return string
     */
    protected function prepareFile($file)
    {
        return ltrim(str_replace('\\', '/', $file), '/');
    }
}
