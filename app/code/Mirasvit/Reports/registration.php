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
 * @package   mirasvit/module-reports
 * @version   1.5.3
 * @copyright Copyright (C) 2023 Mirasvit (https://mirasvit.com/)
 */


$registration = dirname(dirname(dirname(__DIR__))) . '/vendor/mirasvit/module-reports/src/Reports/registration.php';
if (file_exists($registration)) {
    # module was already installed via composer
    return;
}
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Mirasvit_Reports',
    __DIR__
);