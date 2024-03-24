<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\WishlistAdminEmail\Block\Wishlist;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Space\WishlistAdminEmail\Api\ConfigInterface;
use Magento\Wishlist\Model\Item;
use Magento\Wishlist\Model\Wishlist;

class WishlistItems extends Template
{
    /**
     * Wishlist items template
     */
    private const TEMPLATE = 'wishlist/wishlist-items.phtml';

    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * Constructor
     *
     * @param Context $context
     * @param ConfigInterface $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigInterface $config,
        array $data = []
    ) {
        $this->config = $config;
        parent::__construct($context, $data);
        $this->setTemplate(self::TEMPLATE);
    }

    /**
     * Get wishlist item name
     *
     * @return string
     * @throws LocalizedException
     */
    public function getWishlistItemName(): string
    {
        $item = $this->getItem();

        return $item->getProduct()->getName();
    }

    /**
     * Get email items selection
     *
     * @return int
     */
    public function getItemsSelection(): int
    {
        return $this->config->getItemsSelection();
    }

    /**
     * Get wishlist
     *
     * @return Wishlist
     */
    private function getWishlist(): Wishlist
    {
        return $this->getData('wishlist');
    }

    /**
     * Get wishlist item
     *
     * @return Item
     */
    private function getItem(): Item
    {
        return $this->getData('item');
    }
}
