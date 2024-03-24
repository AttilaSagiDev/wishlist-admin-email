<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\WishlistAdminEmail\Block\Wishlist;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Space\WishlistAdminEmail\Api\ConfigInterface;

class WishlistItems extends Template
{
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
        $this->setTemplate('wishlist/wishlist-items.phtml');
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
}
