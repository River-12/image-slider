<?php

namespace Riverstone\ImageSlider\Model\Slider;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Riverstone\ImageSlider\Model\ResourceModel\Slider\CollectionFactory;
use Riverstone\ImageSlider\Model\SliderFactory;
use Riverstone\ImageSlider\Model\ResourceModel\Slider;
use Psr\Log\LoggerInterface;

class DataProvider extends AbstractDataProvider implements DataProviderInterface
{
    /**
     * Create Collection
     *
     * @var CollectionFactory
     */
    protected $collection;
    /**
     * New Store
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * Set Data
     *
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    /**
     * New File system
     *
     * @var Filesystem
     */
    protected $_filesystem;
    /**
     * New Mime
     *
     * @var Mime
     */
    protected $_mime;
    /**
     * New Request
     *
     * @var RequestInterface
     */
    protected $request;
    /**
     * Model Factory
     *
     * @var SliderFactory
     */
    protected $sliderFactory;
    /**
     * New pool
     *
     * @var PoolInterface
     */
    protected $pool;
    /**
     * New Resource
     *
     * @var Slider
     */
    protected $resourceModel;
    /**
     * Check Log
     *
     * @var LoggerInterface
     */
    protected $logger;
    protected $_loadedData;


    /**
     * Constructor params
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param StoreManagerInterface $storeManager
     * @param Filesystem $filesystem
     * @param Mime $mime
     * @param DataPersistorInterface $dataPersistor
     * @param PoolInterface $pool
     * @param RequestInterface $request
     * @param SliderFactory $sliderFactory
     * @param Slider $resourceModel
     * @param LoggerInterface $logger
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        Mime $mime,
        DataPersistorInterface $dataPersistor,
        PoolInterface $pool,
        RequestInterface $request,
        SliderFactory $sliderFactory,
        Slider $resourceModel,
        LoggerInterface $logger,
        array $meta = [],
        array $data = []
    ) {
        $this->request = $request;
        $this->dataPersistor = $dataPersistor;
        $this->collection = $collectionFactory->create();
        $this->sliderFactory = $sliderFactory;
        $this->storeManager = $storeManager;
        $this->_filesystem = $filesystem;
        $this->_mime = $mime;
        $this->pool = $pool;
        $this->resourceModel = $resourceModel;
        $this->logger = $logger;

        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
    }

    /**
     * GetData
     *
     * @return array|mixed
     */
    public function getData()
    {
        $sliderId = $this->request->getParam('id');
        $sliderCollection = $this->sliderFactory->create();
        $this->resourceModel->load($sliderCollection, $sliderId, "id");
        if (isset($this->_loadedData)) {
            return $this->_loadedData;
        }
        $data = $sliderCollection->getData();
        $sliderCollection->setData($data);
        $this->_loadedData[$sliderCollection->getId()] = $sliderCollection->getData();

        $data = $this->dataPersistor->get('slider');
        if (!empty($data)) {
            $slider = $this->collection->getNewEmptyItem();
            $slider->setData($data);
            $this->_loadedData[$slider->getId()] = $slider->getData();
            $this->dataPersistor->clear('slider');
        }
        return $this->_loadedData;
    }

    /**
     * Get Meta
     *
     * @return array
     */
    public function getMeta()
    {
        $sliderId = $this->request->getParam('id');
        $meta = parent::getMeta();
        if ($sliderId) {
            try {
                $modifiers = $this->pool->getModifiersInstances();
                foreach ($modifiers as $modifier) {
                    $meta = $modifier->modifyMeta($meta);
                }
            } catch (LocalizedException $e) {
                $this->logger->critical($e->getMessage());
            }
        }
        return $meta;
    }
}
