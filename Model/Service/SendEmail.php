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
use Magento\Framework\View\LayoutInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;
use Space\WishlistAdminEmail\Block\Wishlist\WishlistItems;

class SendEmail
{
    /**
     * @var TransportBuilder
     */
    private TransportBuilder $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var LayoutInterface
     */
    private LayoutInterface $layout;

    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param LayoutInterface $layout
     * @param ConfigInterface $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        LayoutInterface $layout,
        ConfigInterface $config,
        LoggerInterface $logger
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->layout = $layout;
        $this->config = $config;
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
            $items = $this->getWishlistItemsBlockRenderer();
            if (null !== $items) {
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
                        'items' => $items,
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
            }
        } catch (LocalizedException $e) {
            $this->logger->error($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * Get wishlist items block renderer
     *
     * @return string|null
     */
    private function getWishlistItemsBlockRenderer(): ?string
    {
        $itemsSelectionBlock = $this->layout->createBlock(
            WishlistItems::class,
            'space.wishlist.admin.email.wishlist.items.selection'
        );

        return $itemsSelectionBlock?->toHtml();
    }
}
