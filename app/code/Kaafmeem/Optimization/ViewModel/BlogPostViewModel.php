<?php
declare(strict_types=1);

namespace Kaafmeem\Optimization\ViewModel;

use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Psr\Log\LoggerInterface;

class BlogPostViewModel implements ArgumentInterface
{
    /**
     * @var FilterProvider
     */
    private $filter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        FilterProvider $filter,
        LoggerInterface $logger
    ) {
        $this->filter = $filter;
        $this->logger = $logger;
    }

    /**
     * @param $content
     * @return string
     */
    public function filter($content): string
    {
        try {
            return (string)$this->filter->getBlockFilter()->filter((string)$content);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            return '';
        }
    }
}
