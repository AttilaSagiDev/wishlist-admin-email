<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\WishlistAdminEmail\Test\Unit\Model\Config\Source;

use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Api\Data\GroupSearchResultsInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Space\WishlistAdminEmail\Model\Config\Source\CustomerGroups;

class CustomerGroupsTest extends TestCase
{
    /**
     * @var CustomerGroups
     */
    private CustomerGroups $customerGroups;

    /**
     * @var SearchCriteriaBuilder|MockObject
     */
    private SearchCriteriaBuilder|MockObject $searchCriteriaBuilderMock;

    /**
     * @var GroupRepositoryInterface|MockObject
     */
    private GroupRepositoryInterface|MockObject $groupRepositoryMock;

    /**
     * @var MessageManagerInterface|MockObject
     */
    private MessageManagerInterface|MockObject $messageManagerMock;

    /**
     * @var SearchCriteria|MockObject
     */
    private SearchCriteria|MockObject $searchCriteriaMock;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->groupRepositoryMock = $this->getMockBuilder(GroupRepositoryInterface::class)
            ->getMockForAbstractClass();
        $this->messageManagerMock = $this->getMockBuilder(MessageManagerInterface::class)
            ->getMockForAbstractClass();
        $this->searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->customerGroups = $objectManager->getObject(
            CustomerGroups::class,
            [
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'groupRepository' => $this->groupRepositoryMock,
                'messageManager' => $this->messageManagerMock
            ]
        );
    }

    public function testToOptionArraySuccess(): void
    {
        $group1Mock = $this->getMockBuilder(GroupInterface::class)->getMock();
        $group1Mock->method('getId')->willReturn(1);
        $group1Mock->method('getCode')->willReturn('General');

        $group2Mock = $this->getMockBuilder(GroupInterface::class)->getMock();
        $group2Mock->method('getId')->willReturn(2);
        $group2Mock->method('getCode')->willReturn('Wholesale');

        $searchResultsMock = $this->getMockBuilder(GroupSearchResultsInterface::class)->getMock();
        $searchResultsMock->method('getItems')->willReturn([$group1Mock, $group2Mock]);

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(GroupInterface::ID, 0, 'gt')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($this->searchCriteriaMock);
        $this->groupRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($this->searchCriteriaMock)
            ->willReturn($searchResultsMock);

        $expected = [
            ['value' => 1, 'label' => 'General'],
            ['value' => 2, 'label' => 'Wholesale']
        ];

        $this->assertEquals($expected, $this->customerGroups->toOptionArray());
    }

    public function testToOptionArrayWithLocalizedException(): void
    {
        $exception = new LocalizedException(__('Error'));
        $this->groupRepositoryMock->expects($this->once())
            ->method('getList')
            ->willThrowException($exception);

        $this->searchCriteriaBuilderMock->expects($this->once())->method('addFilter')->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())->method('create')
            ->willReturn($this->searchCriteriaMock);

        $this->messageManagerMock->expects($this->once())
            ->method('addWarningMessage')
            ->with($exception->getMessage());

        $this->assertEquals([], $this->customerGroups->toOptionArray());
    }

    public function testToOptionArrayWithGenericException(): void
    {
        $exception = new \Exception('Generic Error');
        $this->groupRepositoryMock->expects($this->once())
            ->method('getList')
            ->willThrowException($exception);

        $this->searchCriteriaBuilderMock->expects($this->once())->method('addFilter')->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($this->searchCriteriaMock);

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with($exception->getMessage());

        $this->assertEquals([], $this->customerGroups->toOptionArray());
    }

    public function testToOptionArrayWithNoGroups(): void
    {
        $searchResultsMock = $this->getMockBuilder(GroupSearchResultsInterface::class)->getMock();
        $searchResultsMock->method('getItems')->willReturn([]);

        $this->searchCriteriaBuilderMock->expects($this->once())->method('addFilter')->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($this->searchCriteriaMock);
        $this->groupRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($this->searchCriteriaMock)
            ->willReturn($searchResultsMock);

        $this->assertEquals([], $this->customerGroups->toOptionArray());
    }
}
