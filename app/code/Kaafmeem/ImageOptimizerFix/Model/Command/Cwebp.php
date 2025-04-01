<?php
declare(strict_types=1);

namespace Kaafmeem\ImageOptimizerFix\Model\Command;

use Amasty\ImageOptimizer\Model\Command\Cwebp as BaseCwebp;
use Kaafmeem\ImageOptimizerFix\Model\ConfigProvider;
use Magento\Framework\Filesystem;
use Magento\Framework\Shell;
use Psr\Log\LoggerInterface;

class Cwebp extends BaseCwebp
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Shell $shell,
        Filesystem $filesystem,
        LoggerInterface $logger,
        ConfigProvider $configProvider
    ) {
        parent::__construct($shell, $filesystem, $logger);
        $this->configProvider = $configProvider;
    }

    protected function getCommand(): string
    {
        return 'cwebp -q ' . $this->configProvider->getWebpQuality() . ' %s -o %s';
    }
}
