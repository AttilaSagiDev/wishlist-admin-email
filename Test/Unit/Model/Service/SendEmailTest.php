<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\WishlistAdminEmail\Test\Unit\Model\Service;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Space\WishlistAdminEmail\Model\Service\SendEmail;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\LayoutInterface;
use Space\WishlistAdminEmail\Api\ConfigInterface;
use Psr\Log\LoggerInterface;
use Magento\Wishlist\Model\Wishlist;
use Magento\Wishlist\Model\Item;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Store\Model\Store;
use Magento\Framework\Mail\TransportInterface;
use Space\WishlistAdminEmail\Block\Wishlist\WishlistItems;

class SendEmailTest extends TestCase
{
    /**
     * @var SendEmail
     */
    private SendEmail $model;

    /**
     * @var MockObject
     */
    private MockObject $transportBuilderMock;

    /**
     * @var MockObject
     */
    private MockObject $storeManagerMock;

    /**
     * @var MockObject
     */
    private MockObject $layoutMock;

    /**
     * @var MockObject
     */
    private MockObject $configMock;

    /**
     * @var MockObject
     */
    private MockObject $loggerMock;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->transportBuilderMock = $this->createMock(TransportBuilder::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->layoutMock = $this->createMock(LayoutInterface::class);
        $this->configMock = $this->createMock(ConfigInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->model = $objectManager->getObject(
            SendEmail::class,
            [
                'transportBuilder' => $this->transportBuilderMock,
                'storeManager' => $this->storeManagerMock,
                'layout' => $this->layoutMock,
                'config' => $this->configMock,
                'logger' => $this->loggerMock
            ]
        );
    }

    public function testSendWishlistAdminEmailSuccess(): void
    {
        $wishlistMock = $this->createMock(Wishlist::class);
        $itemMock = $this->createMock(Item::class);
        $customerMock = $this->createMock(CustomerInterface::class);
        $transportMock = $this->createMock(TransportInterface::class);

        $storeMock = $this->getMockBuilder(Store::class)
            ->addMethods(['getStoreId'])
            ->disableOriginalConstructor()
            ->getMock();

        $customerMock->method('getFirstname')->willReturn('John');
        $customerMock->method('getLastname')->willReturn('Doe');
        $customerMock->method('getEmail')->willReturn('john@example.com');

        $storeMock->method('getStoreId')->willReturn(1);
        $this->storeManagerMock->method('getStore')->willReturn($storeMock);

        $blockMock = $this->getMockBuilder(WishlistItems::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setData', 'toHtml'])
            ->getMock();

        $this->layoutMock->expects($this->once())
            ->method('createBlock')
            ->with(WishlistItems::class, 'space.wishlist.admin.email.wishlist.items.selection')
            ->willReturn($blockMock);

        $blockMock->method('setData')->willReturnSelf();
        $blockMock->method('toHtml')->willReturn('<html>Mocked Items HTML</html>');

        $this->configMock->method('getEmailTemplate')->willReturn('my_custom_template');
        $this->configMock->method('getCcEmail')->willReturn('cc@example.com');
        $this->configMock->method('getSenderEmail')->willReturn('general');
        $this->configMock->method('getRecipientEmail')->willReturn('admin@example.com');

        $this->transportBuilderMock->method('setTemplateIdentifier')->willReturnSelf();
        $this->transportBuilderMock->method('setTemplateOptions')->willReturnSelf();
        $this->transportBuilderMock->method('setTemplateVars')->willReturnSelf();
        $this->transportBuilderMock->method('setFromByScope')->willReturnSelf();
        $this->transportBuilderMock->method('addTo')->willReturnSelf();
        $this->transportBuilderMock->method('addCc')->willReturnSelf();
        $this->transportBuilderMock->method('getTransport')->willReturn($transportMock);

        $transportMock->expects($this->once())->method('sendMessage');

        $this->model->sendWishlistAdminEmail($wishlistMock, $itemMock, $customerMock);
    }

    public function testSendWishlistAdminEmailLogsException(): void
    {
        $wishlistMock = $this->createMock(Wishlist::class);
        $itemMock = $this->createMock(Item::class);
        $customerMock = $this->createMock(CustomerInterface::class);

        $this->layoutMock->method('createBlock')->willThrowException(new \Exception('Critical Error'));

        $this->loggerMock->expects($this->once())
            ->method('critical')
            ->with('Critical Error');

        $this->model->sendWishlistAdminEmail($wishlistMock, $itemMock, $customerMock);
    }
}
