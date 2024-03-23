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
     * Get recipient bcc_email
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
     * Get bcc email
     *
     * @return string|null
     */
    public function getBccEmail(): ?string
    {
        return $this->scopeConfig->getValue(
            ConfigInterface::XML_PATH_BCC_RECIPIENT,
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
