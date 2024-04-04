<?php

namespace Riverstone\ImageSlider\Model\ResourceModel;

use Exception;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\StoreManagerInterface;
use Riverstone\ImageSlider\Model\ResourceModel\SliderImage as SliderImageResource;
use Riverstone\ImageSlider\Model\ResourceModel\SliderImage\CollectionFactory;

class Slider extends AbstractDb
{
    /**
     * Check Store
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * Entity
     *
     * @var EntityManager
     */
    protected $entityManager;
    /**
     * Collection
     *
     * @var CollectionFactory
     */
    protected $sliderImageCollectionFactory;
    /**
     * Slider resource
     *
     * @var SliderImageResource
     */
    protected $sliderImageResource;
    /**
     * Meta Data
     *
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * Table Initialize
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('riverstone_sliders', 'id');
    }

    /**
     * Constructor params
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param EntityManager $entityManager
     * @param MetadataPool $metadataPool
     * @param CollectionFactory $sliderImageCollectionFactory
     * @param SliderImage $sliderImageResource
     * @param $connectionName
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        CollectionFactory $sliderImageCollectionFactory,
        SliderImageResource $sliderImageResource,
        $connectionName = null
    ) {
        $this->_storeManager = $storeManager;
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
        $this->sliderImageCollectionFactory = $sliderImageCollectionFactory;
        $this->sliderImageResource = $sliderImageResource;
        parent::__construct($context, $connectionName);
    }

    /**
     * Save method
     *
     * @param AbstractModel $object
     * @return $this|Slider
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if (!$this->getIsUniqueSlider($object)) {
            throw new LocalizedException(
                __('The value for "Identifier" should be unique. Another slider is using the same "Identifier"')
            );
        }
        return $this;
    }

    /**
     * Unique Slider
     *
     * @param $object
     * @return bool
     * @throws LocalizedException
     */
    protected function getIsUniqueSlider($object)
    {
        $select = $this->getConnection()->select()
            ->from(['slider' => $this->getMainTable()])
            ->where('slider.identifier = ?  ', $object->getData('identifier'));
        if ($object->getId()) {
            $select->where('slider.id' . ' <> ?', $object->getId());
        }

        if ($this->getConnection()->fetchRow($select)) {
            return false;
        }
        return true;
    }

    /**
     * Delete Method
     *
     * @param AbstractModel $object
     * @return Slider
     */
    protected function _beforeDelete(AbstractModel $object)
    {
        $sliderImageCollection = $this->sliderImageCollectionFactory->create();
        $sliderImageCollection->addFieldToFilter('slider_id', $object->getId());

        foreach ($sliderImageCollection as $sliderImage) {
            try {
                $this->sliderImageResource->delete($sliderImage);
            } catch (Exception $e) {
                $this->_logger->critical($e->getMessage());
            }
        }

        return parent::_beforeDelete($object);
    }
}
