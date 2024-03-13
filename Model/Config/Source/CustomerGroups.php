<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\WishlistAdminEmail\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Framework\Exception\LocalizedException;

class CustomerGroups implements OptionSourceInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var GroupRepositoryInterface
     */
    private GroupRepositoryInterface $groupRepository;

    /**
     * @var MessageManagerInterface
     */
    private MessageManagerInterface $messageManager;

    /**
     * Constructor
     *
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param GroupRepositoryInterface $groupRepository
     * @param MessageManagerInterface $messageManager
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        GroupRepositoryInterface $groupRepository,
        MessageManagerInterface $messageManager
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->groupRepository = $groupRepository;
        $this->messageManager = $messageManager;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $customerGroupOptions = [];

        $customerGroups = $this->getCustomerGroups();
        if (null !== $customerGroups) {
            foreach ($customerGroups as $customerGroup) {
                $customerGroupOptions[] = [
                    'value' => $customerGroup->getId(),
                    'label' => $customerGroup->getCode()
                ];
            }
        }

        return $customerGroupOptions;
    }

    /**
     * Get customer groups
     *
     * @return GroupInterface[]|null
     */
    private function getCustomerGroups(): ?array
    {
        try {
            $this->searchCriteriaBuilder->addFilter(GroupInterface::ID, 0, 'gt');
            $searchCriteria = $this->searchCriteriaBuilder->create();
            return $this->groupRepository->getList($searchCriteria)->getItems();
        } catch (LocalizedException $e) {
            $this->messageManager->addWarningMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return null;
    }
}
