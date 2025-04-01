<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_VideoPlayer
 * @author    Webkul
 * @copyright Copyright (c)   Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\VideoPlayer\Controller\Adminhtml\Gallery;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Get Upload Class
 */
class Upload extends \Magento\Downloadable\Controller\Adminhtml\Downloadable\File implements HttpPostActionInterface
{
    /**
     * @var \Magento\Downloadable\Model\Link
     */
    protected $_link;

    /**
     * @var \Magento\Downloadable\Model\Sample
     */
    protected $_sample;

    /**
     * Downloadable file helper.
     *
     * @var \Magento\Downloadable\Helper\File
     */
    protected $_fileHelper;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    private $storageDatabase;

     /**
      * fileUploader variable
      *
      * @var [Magento\MediaStorage\Model\File\UploaderFactory]
      */
    private $_fileUploaderFactory;
    
    /**
     * storeManager variable
     *
     * @var [Magento\Store\Model\StoreManagerInterface]
     */
    private $storeManager;

    /**
     * mediaDirectory variable
     *
     * @var [Magento\Framework\Filesystem]
     */
    private $_mediaDirectory;

    /**
     * Construction function
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $target = $this->_mediaDirectory->getAbsolutePath('videoplayer/files/');
            
            $uploader = $this->_fileUploaderFactory->create(['fileId' => 'file']);
            
            $uploader->setAllowedExtensions(['mp4', 'webm']);
            
            $uploader->setAllowRenameFiles(true);

            $result = $uploader->save($target);
            
            if (!$result) {
                throw new FileSystemException(
                    __('File can not be moved from temporary folder to the destination folder.')
                );
            }

            unset($result['tmp_name']);

            if (isset($result['file'])) {
                $currentStore = $this->storeManager->getStore();
                $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                
                $result = [
                    'size' => $result['size'],
                    'name' => $result['name'],
                    'uploadedUrl' => $mediaUrl.'videoplayer/files/'.$result['file'],
                ];
            }
        } catch (\Throwable $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
