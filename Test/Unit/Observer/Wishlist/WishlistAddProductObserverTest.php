<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\WishlistAdminEmail\Test\Unit\Observer\Wishlist;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Wishlist\Model\Item;
use Magento\Wishlist\Model\Wishlist;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Space\WishlistAdminEmail\Observer\Wishlist\WishlistAddProductObserver;
use Space\WishlistAdminEmail\Model\Service\SendEmail;
use Magento\Customer\Model\Session as CustomerSession;
use Space\WishlistAdminEmail\Api\ConfigInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event;

class WishlistAddProductObserverTest extends TestCase
{
    /**
     * @var WishlistAddProductObserver
     */
    private WishlistAddProductObserver $model;

    /**
     * @var MockObject
     */
    private MockObject $sendEmailMock;

    /**
     * @var MockObject
     */
    private MockObject $customerSessionMock;

    /**
     * @var MockObject
     */
    private MockObject $configMock;

    /**
     * @var MockObject
     */
    private MockObject $loggerMock;

    /**
     * @var MockObject
     */
    private MockObject $observerMock;

    /**
     * @var MockObject
     */
    private MockObject $eventMock;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->sendEmailMock = $this->createMock(SendEmail::class);
        $this->customerSessionMock = $this->createMock(CustomerSession::class);
        $this->configMock = $this->createMock(ConfigInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->observerMock = $this->createMock(Observer::class);

        $this->eventMock = $this->getMockBuilder(Event::class)
            ->onlyMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            WishlistAddProductObserver::class,
            [
                'sendEmail' => $this->sendEmailMock,
                'customerSession' => $this->customerSessionMock,
                'config' => $this->configMock,
                'logger' => $this->loggerMock
            ]
        );
    }

    public function testExecuteReturnsEarlyIfDisabled(): void
    {
        $this->configMock->expects($this->once())->method('isEnabled')->willReturn(false);
        $this->sendEmailMock->expects($this->never())->method('sendWishlistAdminEmail');

        $this->model->execute($this->observerMock);
    }

    public function testExecuteReturnsEarlyIfCustomerGroupNotAllowed(): void
    {
        $this->configMock->method('isEnabled')->willReturn(true);
        $this->configMock->method('isCustomerSegmentationEnabled')->willReturn(true);
        $this->configMock->method('getEnabledCustomerGroups')->willReturn(['1', '2']);

        $this->customerSessionMock->method('getCustomerGroupId')->willReturn(3);

        $this->sendEmailMock->expects($this->never())->method('sendWishlistAdminEmail');

        $this->model->execute($this->observerMock);
    }

    public function testExecuteSendsEmailSuccessfully(): void
    {
        $this->configMock->method('isEnabled')->willReturn(true);
        $this->configMock->method('isCustomerSegmentationEnabled')->willReturn(false);

        $this->customerSessionMock->method('getCustomerId')->willReturn(10);

        $customerDataMock = $this->createMock(CustomerInterface::class);
        $this->customerSessionMock->method('getCustomerData')->willReturn($customerDataMock);

        $wishlistMock = $this->createMock(Wishlist::class);
        $itemMock = $this->createMock(Item::class);

        $this->observerMock->method('getEvent')->willReturn($this->eventMock);

        $this->eventMock->method('getData')->willReturnMap([
            ['item', null, $itemMock],
            ['wishlist', null, $wishlistMock]
        ]);

        $this->sendEmailMock->expects($this->once())
            ->method('sendWishlistAdminEmail')
            ->with($wishlistMock, $itemMock, $customerDataMock);

        $this->model->execute($this->observerMock);
    }

    public function testExecuteLogsException(): void
    {
        $this->configMock->method('isEnabled')->willReturn(true);
        $this->customerSessionMock->method('getCustomerId')->willReturn(10);

        $wishlistMock = $this->createMock(Wishlist::class);
        $itemMock = $this->createMock(Item::class);
        $customerDataMock = $this->createMock(CustomerInterface::class);

        $this->customerSessionMock->method('getCustomerData')->willReturn($customerDataMock);
        $this->observerMock->method('getEvent')->willReturn($this->eventMock);

        $this->eventMock->method('getData')->willReturnMap([
            ['item', null, $itemMock],
            ['wishlist', null, $wishlistMock]
        ]);

        $exception = new \Exception('Error Message');
        $this->sendEmailMock->method('sendWishlistAdminEmail')
            ->with($wishlistMock, $itemMock, $customerDataMock)
            ->willThrowException($exception);

        $this->loggerMock->expects($this->once())
            ->method('critical')
            ->with('Error Message');

        $this->model->execute($this->observerMock);
    }
}
