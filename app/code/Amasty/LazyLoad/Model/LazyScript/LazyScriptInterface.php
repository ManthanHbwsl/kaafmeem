<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Lazy Load for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\LazyLoad\Model\LazyScript;

interface LazyScriptInterface
{
    public function getName(): string;

    public function getType(): string;

    public function getCode(): string;
}
