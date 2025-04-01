<?php
declare(strict_types=1);

namespace MGS\Guestwishlist\Plugin\Magento\Wishlist\Block;

use Magento\Wishlist\Block\Link;
use MGS\Guestwishlist\Helper\Data as GuestWishlistHelper;

class LinkPlugin
{
    /**
     * @var GuestWishlistHelper
     */
    private $guestHelper;

    /**
     * @param GuestWishlistHelper $guestHelper
     */
    public function __construct(GuestWishlistHelper $guestHelper)
    {
        $this->guestHelper = $guestHelper;
    }

    /**
     * @param Link $subject
     * @param callable $proceed
     * @return string
     */
    public function aroundGetHref(Link $subject, callable $proceed): string
    {
        if (!$this->guestHelper->isLoggedIn() && $this->guestHelper->isModuleEnable()) {
            return $subject->getUrl('guestwishlist');
        }

        return $proceed();
    }
}
