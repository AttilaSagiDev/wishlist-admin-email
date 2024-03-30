<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\WishlistAdminEmail\Api;

/**
 * Contact module configuration
 *
 * @api
 * @since 100.2.0
 */
interface ConfigInterface
{
    /**
     * Enabled config path
     */
    public const XML_PATH_ENABLED = 'wishlist_admin_email/wishlist_admin_email_config/enabled';

    /**
     * Store config is customer segmentation enabled
     */
    public const XML_PATH_SEGMENTATION = 'wishlist_admin_email/wishlist_admin_email_config/enable_segmentation';

    /**
     * Store config enabled customer groups
     */
    public const XML_PATH_CUSTOMER_GROUPS = 'wishlist_admin_email/wishlist_admin_email_config/customer_groups';

    /**
     * Wishlist notification items config path
     */
    public const XML_PATH_EMAIL_ITEMS_SELECTION = 'wishlist_admin_email/wishlist_admin_email_email/items_selection';

    /**
     * Recipient email config path
     */
    public const XML_PATH_EMAIL_RECIPIENT = 'wishlist_admin_email/wishlist_admin_email_email/recipient_email';

    /**
     * Recipient cc email config path
     */
    public const XML_PATH_CC_RECIPIENT = 'wishlist_admin_email/wishlist_admin_email_email/cc_email';

    /**
     * Sender email config path
     */
    public const XML_PATH_EMAIL_SENDER = 'wishlist_admin_email/wishlist_admin_email_email/sender_email_identity';

    /**
     * Email template config path
     */
    public const XML_PATH_EMAIL_TEMPLATE = 'wishlist_admin_email/wishlist_admin_email_email/email_template';

    /**
     * Check if wishlist admin email module is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Check if customer segmentation enabled
     *
     * @return bool
     */
    public function isCustomerSegmentationEnabled(): bool;

    /**
     * Get enabled customer groups
     *
     * @return array|bool
     */
    public function getEnabledCustomerGroups(): ?array;

    /**
     * Get email items selection
     *
     * @return int
     */
    public function getItemsSelection(): int;

    /**
     * Get recipient email
     *
     * @return string
     */
    public function getRecipientEmail(): string;

    /**
     * Get cc email
     *
     * @return string|null
     */
    public function getCcEmail(): ?string;

    /**
     * Get sender email
     *
     * @return string
     */
    public function getSenderEmail(): string;

    /**
     * Get email template
     *
     * @return string
     */
    public function getEmailTemplate(): string;
}
