<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\WishlistAdminEmail\Test\Unit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Space\WishlistAdminEmail\Api\ConfigInterface;
use Space\WishlistAdminEmail\Model\Config;

class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    private ScopeConfigInterface|MockObject $scopeConfigMock;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class)
            ->getMockForAbstractClass();

        $this->config = $objectManager->getObject(
            Config::class,
            [
                'scopeConfig' => $this->scopeConfigMock
            ]
        );
    }

    public function testIsEnabled(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(ConfigInterface::XML_PATH_ENABLED, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn(true);

        $this->assertTrue($this->config->isEnabled());
    }

    public function testIsCustomerSegmentationEnabled(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(ConfigInterface::XML_PATH_SEGMENTATION, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn(true);

        $this->assertTrue($this->config->isCustomerSegmentationEnabled());
    }

    public function testGetEnabledCustomerGroups(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(ConfigInterface::XML_PATH_CUSTOMER_GROUPS, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn('1,2,3');

        $this->assertEquals(['1', '2', '3'], $this->config->getEnabledCustomerGroups());
    }

    public function testGetEnabledCustomerGroupsEmpty(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(ConfigInterface::XML_PATH_CUSTOMER_GROUPS, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn('');

        $this->assertNull($this->config->getEnabledCustomerGroups());
    }

    public function testGetItemsSelection(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(ConfigInterface::XML_PATH_EMAIL_ITEMS_SELECTION, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn(10);

        $this->assertEquals(10, $this->config->getItemsSelection());
    }

    public function testGetRecipientEmail(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(ConfigInterface::XML_PATH_EMAIL_RECIPIENT, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn('test@example.com');

        $this->assertEquals('test@example.com', $this->config->getRecipientEmail());
    }

    public function testGetCcEmail(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(ConfigInterface::XML_PATH_CC_RECIPIENT, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn('cc@example.com');

        $this->assertEquals('cc@example.com', $this->config->getCcEmail());
    }

    public function testGetSenderEmail(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(ConfigInterface::XML_PATH_EMAIL_SENDER, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn('sender@example.com');

        $this->assertEquals('sender@example.com', $this->config->getSenderEmail());
    }

    public function testGetEmailTemplate(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(ConfigInterface::XML_PATH_EMAIL_TEMPLATE, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn('template_id');

        $this->assertEquals('template_id', $this->config->getEmailTemplate());
    }
}
