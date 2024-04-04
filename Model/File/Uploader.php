<?php

namespace Riverstone\ImageSlider\Model\File;

use Magento\MediaStorage\Model\File\Uploader as MediaUploader;

class Uploader extends MediaUploader
{
    protected $_allowRenameFiles = true;
}
