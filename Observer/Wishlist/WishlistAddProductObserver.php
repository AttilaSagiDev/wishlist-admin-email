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
use Magento\Customer\Model\Session as CustomerSession;
use Space\WishlistAdminEmail\Api\ConfigInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\LocalizedException;

class WishlistAddProductObserver implements ObserverInterface
{
    /**
     * @var SendEmail
     */
    private SendEmail $sendEmail;

    /**
     * @var CustomerSession
     */
    private CustomerSession $customerSession;

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
     * @param SendEmail $sendEmail
     * @param CustomerSession $customerSession
     * @param ConfigInterface $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        SendEmail $sendEmail,
        CustomerSession $customerSession,
        ConfigInterface $config,
        LoggerInterface $logger
    ) {
        $this->sendEmail = $sendEmail;
        $this->customerSession = $customerSession;
        $this->config = $config;
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
            try {
                if ($this->config->isCustomerSegmentationEnabled()
                    && is_array($this->config->getEnabledCustomerGroups())
                    && !empty($this->config->getEnabledCustomerGroups())
                    && !in_array(
                        $this->customerSession->getCustomerGroupId(),
                        $this->config->getEnabledCustomerGroups()
                    )
                ) {
                    return;
                }

                $item = $observer->getEvent()->getData('item');
                $wishlist = $observer->getEvent()->getData('wishlist');
                $this->sendEmail->sendWishlistAdminEmail($wishlist, $item);
            } catch (LocalizedException $e) {
                $this->logger->error($e->getMessage());
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }
    }
}
