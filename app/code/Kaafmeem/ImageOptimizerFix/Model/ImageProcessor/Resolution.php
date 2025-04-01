<?php
declare(strict_types=1);

namespace Kaafmeem\ImageOptimizerFix\Model\ImageProcessor;

use Amasty\ImageOptimizer\Api\Data\QueueInterface;
use Amasty\ImageOptimizer\Model\Command\CommandProvider;
use Amasty\ImageOptimizer\Model\ImageProcessor\Resolution as BaseResolution;
use Kaafmeem\ImageOptimizerFix\Model\ConfigProvider;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\Adapter\Gd2;

class Resolution extends BaseResolution
{
    /**
     * @var Gd2
     */
    protected $gd2;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Gd2 $gd2,
        Filesystem $filesystem,
        CommandProvider $webpCommandProvider,
        ConfigProvider $configProvider
    ) {
        parent::__construct($gd2, $filesystem, $webpCommandProvider);
        $this->gd2 = $gd2;
        $this->configProvider = $configProvider;
    }

    public function process(QueueInterface $queue): void
    {
        $this->gd2->quality($this->configProvider->getJpegQuality());

        parent::process($queue);
    }
}
