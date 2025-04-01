<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-banner
 * @version   1.1.10
 * @copyright Copyright (C) 2023 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Banner\Block\Placeholder;

use Mirasvit\Banner\Api\Data\PlaceholderInterface;

class PopupRenderer extends AbstractRenderer
{
    protected $_template = 'Mirasvit_Banner::placeholder/popupRenderer.phtml';

    public function getBanners()
    {
        return $this->bannerService->getApplicableBanners($this->getPlaceholder(), 1);
    }

    public function getPopupConfig(): array
    {
        $placeholder = $this->getPlaceholder();

        $config = [PlaceholderInterface::ID => $placeholder->getId()];

        $additional = $placeholder->getAdditional();

        if ($additional) {
            $config['cookie_lifetime'] = !isset($additional['cookie_lifetime']) || $additional['cookie_lifetime'] === ''
                ? 24
                : (int)$additional['cookie_lifetime'];

            $config['delay'] = !isset($additional['delay']) || $additional['delay'] === ''
                ? 3
                : (int)$additional['delay'];
        }

        return $config;
    }

    public function getPositionClass(): string
    {
        $position = 'bottom-right';

        $additional = $this->getPlaceholder()->getAdditional();

        if (!$additional || !isset($additional['popup_position'])) {
            return $position;
        }

        return $additional['popup_position'];
    }
}
