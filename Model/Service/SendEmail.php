<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\WishlistAdminEmail\Model\Service;

use Magento\Framework\Mail\Template\TransportBuilder;
use Psr\Log\LoggerInterface;


use Magento\Framework\Exception\LocalizedException;

class SendEmail
{
    /**
     * @var TransportBuilder
     */
    private TransportBuilder $transportBuilder;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param TransportBuilder $transportBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        LoggerInterface $logger
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->logger = $logger;
    }

    /**
     * Send wishlist admin email
     *
     * @return void
     */
    public function sendWishlistAdminEmail(): void
    {
        try {
            $this->logger->info('sendWishlistAdminEmail');
        } catch (LocalizedException $e) {
            $this->logger->error($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
