<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_VideoPlayer
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\VideoPlayer\Helper;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Helper class
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * Customer Session variable
     *
     * @var [Magento\Customer\Model\Session]
     */
    protected $_customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
   
    /**
     *
     * @param Session $customerSession
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        CustomerSession $customerSession,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Get Module configuration function
     *
     * @param string $path
     * @return string
     */
    public function getModuleConfig($path)
    {
        return $this->scopeConfig
            ->getValue(
                'videoplayer/'.$path,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
    }

    /**
     * Get Video Configuration function
     *
     * @return array
     */
    public function getVideoConfiguration()
    {
        $active = $this->getModuleConfig('general_settings/active');
        $autoPlay = $this->getModuleConfig('general_settings/video_productplay');
        $fullScreen = $this->getModuleConfig('general_settings/video_fullscreen');
        $loop = $this->getModuleConfig('general_settings/video_loop');
        $playerOptions = $this->getModuleConfig('player_settings/options');
        $playerColor = $this->getModuleConfig('player_settings/primary_color');
        return [
            'active' => (boolean)$active??0,
            'autoplay' => (boolean)$autoPlay??0,
            'fullscreen' => (boolean)$fullScreen??0,
            'loop' => (boolean)$loop??0,
            'playercolor' => $playerColor,
            'playeroptions' => $playerOptions!= '' ? explode(',', $playerOptions) : [],
        ];
    }
}
