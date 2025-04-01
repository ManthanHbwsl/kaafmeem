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
namespace Webkul\VideoPlayer\ViewModel;

/**
 * Set VideoPlayerHelper class
 */
class VideoPlayerHelper implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var Webkul\VideoPlayer\Helper\Data
     */
    private $helper;

    /**
     * JsonEncoder variable
     *
     * @var [Magento\Framework\Json\EncoderInterface]
     */
    private $jsonEncoder;

    /**
     * Construction function
     *
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Webkul\VideoPlayer\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Webkul\VideoPlayer\Helper\Data $helper
    ) {
        $this->jsonEncoder = $jsonEncoder;
        $this->helper = $helper;
    }

    /**
     * Get VideoConfiguration Json function
     *
     * @return array
     */
    public function getVideoConfigurationJsonData()
    {
        return $this->jsonEncoder->encode($this->helper->getVideoConfiguration());
    }
}
