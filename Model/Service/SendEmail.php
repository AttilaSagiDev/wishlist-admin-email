<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\WishlistAdminEmail\Model\Service;

use Magento\Framework\Mail\Template\TransportBuilder;
use Space\WishlistAdminEmail\Api\ConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;

class SendEmail
{
    /**
     * @var TransportBuilder
     */
    private TransportBuilder $transportBuilder;

    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param TransportBuilder $transportBuilder
     * @param ConfigInterface $config
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        ConfigInterface $config,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->config = $config;
        $this->storeManager = $storeManager;
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
            $bccEmail = $this->config->getBccEmail() ? $this->config->getBccEmail() : '';
            $transport = $this->transportBuilder->setTemplateIdentifier(
                $this->config->getEmailTemplate()
            )->setTemplateOptions(
                [
                    'area' => Area::AREA_FRONTEND,
                    'store' => $this->storeManager->getStore()->getStoreId(),
                ]
            )->setTemplateVars(
                [
                    'items' => 'abc123',
                    'store' => $this->storeManager->getStore()
                ]
            )->setFromByScope(
                $this->config->getSenderEmail()
            )->addTo(
                $this->config->getRecipientEmail()
            )->addBcc(
                $bccEmail
            )->getTransport();

            $transport->sendMessage();
        } catch (LocalizedException $e) {
            $this->logger->error($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
