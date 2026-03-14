<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\WishlistAdminEmail\Test\Unit\Model\Config\Source;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Space\WishlistAdminEmail\Model\Config\Source\ItemsSelection;

class ItemsSelectionTest extends TestCase
{
    /**
     * @var ItemsSelection
     */
    private ItemsSelection $itemsSelection;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->itemsSelection = $objectManager->getObject(ItemsSelection::class);
    }

    public function testToOptionArray(): void
    {
        $expected = [
            ['value' => ItemsSelection::ONLY_NEWLY_ADDED, 'label' => __('Send only newly added')],
            ['value' => ItemsSelection::WHOLE_WISHLIST, 'label' => __('Send entire wishlist')]
        ];

        $this->assertEquals($expected, $this->itemsSelection->toOptionArray());
    }
}
