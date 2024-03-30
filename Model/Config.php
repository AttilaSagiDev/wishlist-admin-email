<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\WishlistAdminEmail\Model;

use Magento\Store\Model\ScopeInterface;
use Space\WishlistAdminEmail\Api\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Config implements ConfigInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check if wishlist admin email module is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            ConfigInterface::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Check if customer segmentation enabled
     *
     * @return bool
     */
    public function isCustomerSegmentationEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            ConfigInterface::XML_PATH_SEGMENTATION,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get enabled customer groups
     *
     * @return array|false
     */
    public function getEnabledCustomerGroups(): ?array
    {
        $customerGroups = $this->scopeConfig->getValue(
            ConfigInterface::XML_PATH_CUSTOMER_GROUPS,
            ScopeInterface::SCOPE_WEBSITE
        );

        if (!empty($customerGroups)) {
            return explode(',', $customerGroups);
        }

        return false;
    }

    /**
     * Get email items selection
     *
     * @return int
     */
    public function getItemsSelection(): int
    {
        return (int)$this->scopeConfig->getValue(
            ConfigInterface::XML_PATH_EMAIL_ITEMS_SELECTION,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get recipient email
     *
     * @return string
     */
    public function getRecipientEmail(): string
    {
        return $this->scopeConfig->getValue(
            ConfigInterface::XML_PATH_EMAIL_RECIPIENT,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get cc email
     *
     * @return string|null
     */
    public function getCcEmail(): ?string
    {
        return $this->scopeConfig->getValue(
            ConfigInterface::XML_PATH_CC_RECIPIENT,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get sender email
     *
     * @return string
     */
    public function getSenderEmail(): string
    {
        return $this->scopeConfig->getValue(
            ConfigInterface::XML_PATH_EMAIL_SENDER,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get email template
     *
     * @return string
     */
    public function getEmailTemplate(): string
    {
        return $this->scopeConfig->getValue(
            ConfigInterface::XML_PATH_EMAIL_TEMPLATE,
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}
