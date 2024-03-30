<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\WishlistAdminEmail\Block\Wishlist;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Space\WishlistAdminEmail\Api\ConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Wishlist\Model\ResourceModel\Item\Collection as WishlistItemCollection;
use Magento\Framework\Exception\NoSuchEntityException;
use Space\WishlistAdminEmail\Model\Config\Source\ItemsSelection;
use Magento\Wishlist\Model\Item;
use Magento\Wishlist\Model\Wishlist;
use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\Render as PriceRenderer;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Catalog\Api\Data\ProductInterface;

class WishlistItems extends Template
{
    /**
     * Wishlist items template
     */
    private const TEMPLATE = 'wishlist/wishlist-items.phtml';

    /**
     * @var ItemResolverInterface
     */
    private ItemResolverInterface $itemResolver;

    /**
     * @var ImageHelper
     */
    private ImageHelper $imageHelper;

    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * Constructor
     *
     * @param Context $context
     * @param ImageHelper $imageHelper
     * @param ConfigInterface $config
     * @param array $data
     * @param ItemResolverInterface|null $itemResolver
     */
    public function __construct(
        Context $context,
        ImageHelper $imageHelper,
        ConfigInterface $config,
        array $data = [],
        ItemResolverInterface $itemResolver = null
    ) {
        $this->imageHelper = $imageHelper;
        $this->config = $config;
        parent::__construct($context, $data);
        $this->setTemplate(self::TEMPLATE);
        $this->itemResolver = $itemResolver ?? ObjectManager::getInstance()->get(ItemResolverInterface::class);
    }

    /**
     * Get to show all items from the wishlist
     *
     * @return bool
     */
    public function getShowAllItems(): bool
    {
        return $this->config->getItemsSelection() === ItemsSelection::WHOLE_WISHLIST;
    }

    /**
     * Get wishlist items
     *
     * @return WishlistItemCollection|null
     */
    public function getWishlistItems(): ?WishlistItemCollection
    {
        try {
            return null !== $this->getWishlist()
                ? $this->getWishlist()->getItemCollection()
                : null;
        } catch (NoSuchEntityException $e) {
            $this->_logger->error($e->getMessage());
        } catch (\Exception $e) {
            $this->_logger->critical($e->getMessage());
        }

        return null;
    }

    /**
     * Get product
     *
     * @param Item $item
     * @return ProductInterface
     */
    public function getProduct(Item $item): ProductInterface
    {
        return $this->itemResolver->getFinalProduct($item);
    }

    /**
     * Return HTML block with tier price
     *
     * @param Product $product
     * @return string
     */
    public function getProductPriceHtml(Product $product): string
    {
        /** @var PriceRenderer $priceRender */
        $priceRender = $this->_layout->getBlock('product.price.render.default');
        if (!$priceRender) {
            $priceRender = $this->_layout->createBlock(
                PriceRenderer::class,
                'product.price.render.default',
                ['data' => ['price_render_handle' => 'catalog_product_prices']]
            );
        }

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                FinalPrice::PRICE_CODE,
                $product,
                [
                    'display_minimal_price' => true,
                    'use_link_for_as_low_as' => false,
                    'include_container' => false,
                    'zone' => PriceRenderer::ZONE_EMAIL
                ]
            );
        }

        return $price;
    }

    /**
     * Retrieve product image
     *
     * @param Product $product
     * @return string
     */
    public function getImage(Product $product): string
    {
        return $this->imageHelper->init($product, 'product_small_image')->getUrl();
    }

    /**
     * Get wishlist item
     *
     * @return Item|null
     */
    public function getItem(): ?Item
    {
        return $this->getData('item');
    }

    /**
     * Get wishlist
     *
     * @return Wishlist
     */
    private function getWishlist(): Wishlist
    {
        return $this->getData('wishlist');
    }
}
