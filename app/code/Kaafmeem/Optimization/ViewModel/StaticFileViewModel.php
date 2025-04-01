<?php
declare(strict_types=1);

namespace Kaafmeem\Optimization\ViewModel;

use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class StaticFileViewModel implements ArgumentInterface
{
    private const OVAL_LOADER_IMAGE = 'images/oval.svg';

    /**
     * @var AssetRepository
     */
    private $assetRepository;

    public function __construct(AssetRepository $assetRepository)
    {
        $this->assetRepository = $assetRepository;
    }

    public function getStaticFileUrl(string $fileId): string
    {
        return $this->assetRepository->getUrl($fileId);
    }

    public function getOvalLoaderUrl(): string
    {
        return $this->getStaticFileUrl(self::OVAL_LOADER_IMAGE);
    }
}
