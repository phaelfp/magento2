<?php

namespace MundiPagg\MundiPagg\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mundipagg\Core\Recurrence\Aggregates\Plan;
use Mundipagg\Core\Recurrence\Interfaces\RecurrenceEntityInterface;
use Mundipagg\Core\Recurrence\Services\PlanService;
use Mundipagg\Core\Recurrence\Services\RecurrenceService;
use MundiPagg\MundiPagg\Concrete\Magento2CoreSetup;
use MundiPagg\MundiPagg\Helper\ProductPlanHelper;
use MundiPagg\MundiPagg\Helper\RecurrenceProductHelper;

class UpdateProductPlanObserver implements ObserverInterface
{
    /**
     * @var RecurrenceProductHelper
     */
    protected $recurrenceProductHelper;

    public function __construct(RecurrenceProductHelper $recurrenceProductHelper)
    {
        Magento2CoreSetup::bootstrap();
        $this->recurrenceProductHelper = $recurrenceProductHelper;
    }

    public function execute(Observer $observer)
    {
       $event = $observer->getEvent();
       $product = $event->getProduct();

       if (!$product) {
           return $this;
       }

       $productId = $product->getEntityId();
       $recurrenceService = new RecurrenceService();
       $recurrence = $recurrenceService->getRecurrenceProductByProductId($productId);

       if (!$recurrence || $recurrence->getRecurrenceType() !== Plan::RECURRENCE_TYPE) {
           return $this;
       }

       return $this->updatePlan($recurrence, $product);
    }

    protected function updatePlan(RecurrenceEntityInterface $recurrence)
    {
        try{
            ProductPlanHelper::mapperProductPlan($recurrence);
            $service = new PlanService();
            $service->updatePlanAtMundipagg($recurrence);
            $service->save($recurrence);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }

        return $this;
    }
}