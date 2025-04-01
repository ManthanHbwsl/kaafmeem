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


declare(strict_types=1);


namespace Mirasvit\Banner\Placeholder;


class SliderRenderer extends AbstractRenderer
{
    public function getCode()
    {
        return 'slider';
    }

    public function getLabel()
    {
        return 'Slider';
    }

    public function getBlockClass()
    {
        return \Mirasvit\Banner\Block\Placeholder\SliderRenderer::class;
    }


}
