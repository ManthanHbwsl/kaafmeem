<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Page Speed Tools for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\PageSpeedTools\Model\Output;

interface OutputProcessorInterface
{
    /**
     * @param string &$output
     *
     * @return bool
     */
    public function process(string &$output): bool;
}
