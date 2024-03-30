<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\WishlistAdminEmail\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ItemsSelection implements OptionSourceInterface
{
    /**
     * Only newly added
     */
    public const ONLY_NEWLY_ADDED = 1;

    /**
     * Whole wishlist
     */
    public const WHOLE_WISHLIST = 2;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::ONLY_NEWLY_ADDED, 'label' => __('Send only newly added')],
            ['value' => self::WHOLE_WISHLIST, 'label' => __('Send entire wishlist')]
        ];
    }
}
