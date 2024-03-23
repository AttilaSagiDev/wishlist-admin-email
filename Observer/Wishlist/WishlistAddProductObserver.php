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
use Psr\Log\LoggerInterface;

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
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param ConfigInterface $config
     * @param SendEmail $sendEmail
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConfigInterface $config,
        SendEmail $sendEmail,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->sendEmail = $sendEmail;
        $this->logger = $logger;
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
            $this->logger->info('Space_WishlistAdminEmail');
            $this->logger->info($observer->getEvent()->getData('product')->getName());
            $this->sendEmail->sendWishlistAdminEmail();
        }
    }
}
