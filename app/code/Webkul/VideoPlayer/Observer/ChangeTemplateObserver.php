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

namespace Webkul\VideoPlayer\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Define ChangeTemplateObserver class
 */
class ChangeTemplateObserver implements ObserverInterface
{
    /**
     * Helper variable
     *
     * @var [Webkul\VideoPlayer\Helper\Data]
     */
    private $helper;

    /**
     * ViewModelHelper variable
     *
     * @var [Webkul\VideoPlayer\ViewModel\VideoPlayerHelper]
     */
    private $viewModelHelper;
    
    /**
     * Construct function
     *
     * @param \Webkul\VideoPlayer\Helper\Data $helper
     * @param \Webkul\VideoPlayer\ViewModel\VideoPlayerHelper $viewModelHelper
     */
    public function __construct(
        \Webkul\VideoPlayer\Helper\Data $helper,
        \Webkul\VideoPlayer\ViewModel\VideoPlayerHelper $viewModelHelper
    ) {
        $this->helper = $helper;
        $this->viewModelHelper = $viewModelHelper;
    }

    /**
     * Execute function
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->getModuleConfig('general_settings/active')) {
            $observer->getBlock()->setData('view_model', $this->viewModelHelper);
            $observer->getBlock()->setTemplate('Webkul_VideoPlayer::helper/gallery.phtml');
        }
    }
}
