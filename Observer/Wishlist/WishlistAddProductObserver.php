<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\WishlistAdminEmail\Observer\Wishlist;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Space\WishlistAdminEmail\Model\Service\SendEmail;
use Space\WishlistAdminEmail\Api\ConfigInterface;

class WishlistAddProductObserver implements ObserverInterface
{
    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * @var SendEmail
     */
    private SendEmail $sendEmail;

    /**
     * Constructor
     *
     * @param ConfigInterface $config
     * @param SendEmail $sendEmail
     */
    public function __construct(
        ConfigInterface $config,
        SendEmail $sendEmail
    ) {
        $this->config = $config;
        $this->sendEmail = $sendEmail;
    }

    /**
     * Add product to wishlist action
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        if ($this->config->isEnabled()) {
            $item = $observer->getEvent()->getData('item');
            $wishlist = $observer->getEvent()->getData('wishlist');
            $this->sendEmail->sendWishlistAdminEmail($wishlist, $item);
        }
    }
}
