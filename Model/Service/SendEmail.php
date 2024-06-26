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
use Magento\Wishlist\Model\Wishlist;
use Magento\Wishlist\Model\Item;
use Magento\Customer\Api\Data\CustomerInterface;
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
     * @param Wishlist $wishlist
     * @param Item $item
     * @param CustomerInterface $customer
     * @return void
     */
    public function sendWishlistAdminEmail(Wishlist $wishlist, Item $item, CustomerInterface $customer): void
    {
        try {
            $itemsBlock = $this->getWishlistItemsBlockRenderer($wishlist, $item);
            if (null !== $itemsBlock) {
                $ccEmail = $this->config->getCcEmail() ? $this->config->getCcEmail() : '';
                $transport = $this->transportBuilder->setTemplateIdentifier(
                    $this->config->getEmailTemplate()
                )->setTemplateOptions(
                    [
                        'area' => Area::AREA_FRONTEND,
                        'store' => $this->storeManager->getStore()->getStoreId(),
                    ]
                )->setTemplateVars(
                    [
                        'items' => $itemsBlock,
                        'store' => $this->storeManager->getStore(),
                        'customer_name' => $customer->getFirstname() . ' ' . $customer->getLastname(),
                        'customer_email' => $customer->getEmail(),
                    ]
                )->setFromByScope(
                    $this->config->getSenderEmail()
                )->addTo(
                    $this->config->getRecipientEmail()
                )->addCc(
                    $ccEmail
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
     * @param Wishlist $wishlist
     * @param Item $item
     * @return string|null
     */
    private function getWishlistItemsBlockRenderer(Wishlist $wishlist, Item $item): ?string
    {
        $itemsSelectionBlock = $this->layout->createBlock(
            WishlistItems::class,
            'space.wishlist.admin.email.wishlist.items.selection'
        )->setData('wishlist', $wishlist)->setData('item', $item);

        return $itemsSelectionBlock?->toHtml();
    }
}
