<?php

namespace Riverstone\ImageSlider\Model\ResourceModel;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\FileSystem\Driver\File;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Riverstone\ImageSlider\Helper\SliderImage as SliderImageHelper;

class SliderImage extends AbstractDb
{
    /**
     * New File
     *
     * @var File
     */
    protected $file;
    /**
     * Helper
     *
     * @var SliderImageHelper
     */
    protected $sliderImageHelper;

    /**
     * Constructor Params
     *
     * @param Context $context
     * @param File $file
     * @param SliderImageHelper $sliderImageHelper
     * @param $connectionName
     */
    public function __construct(
        Context $context,
        File $file,
        SliderImageHelper $sliderImageHelper,
        $connectionName = null
    ) {
        $this->file = $file;
        $this->sliderImageHelper = $sliderImageHelper;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('riverstone_slider_images', 'id');
    }

    /**
     * Delete Method
     *
     * @param AbstractModel $object
     * @return SliderImage
     */
    protected function _afterDelete(AbstractModel $object)
    {
        try {
            if ($object->getImagePath()) {
                $this->file->deleteFile($this->sliderImageHelper->getSliderImagePath() . $object->getImagePath());
            }
        } catch (FileSystemException $e) {
            $this->_logger->critical($e->getMessage());
        }
        return parent::_afterDelete($object);
    }
}
