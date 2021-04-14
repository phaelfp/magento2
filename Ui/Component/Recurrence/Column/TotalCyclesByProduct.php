<?php

namespace MundiPagg\MundiPagg\Ui\Component\Recurrence\Column;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use MundiPagg\MundiPagg\Concrete\Magento2CoreSetup;
use MundiPagg\MundiPagg\Helper\RecurrenceProductHelper;

class TotalCyclesByProduct extends Column
{
    private $objectManager;
    /**
     * @var RecurrenceProductHelper
     */
    private $recurrenceProductHelper;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->objectManager = ObjectManager::getInstance();
        Magento2CoreSetup::bootstrap();
        $this->recurrenceProductHelper = new RecurrenceProductHelper();
    }

    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        $fieldName = $this->getData('name');
        foreach ($dataSource['data']['items'] as &$item) {
            $item[$fieldName] =
                $this->recurrenceProductHelper
                ->getHighestProductCycle($item['code'], $item['plan_id']);
        }

        return $dataSource;
    }
}