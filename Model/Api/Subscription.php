<?php

namespace MundiPagg\MundiPagg\Model\Api;

use Magento\Framework\Webapi\Rest\Request;
use Mundipagg\Core\Kernel\Services\LocalizationService;
use Mundipagg\Core\Kernel\Services\MoneyService;
use Mundipagg\Core\Recurrence\Services\SubscriptionService;
use MundiPagg\MundiPagg\Api\SubscriptionApiInterface;
use MundiPagg\MundiPagg\Concrete\Magento2CoreSetup;

class Subscription implements SubscriptionApiInterface
{

    /**
     * @var Request
     */
    protected $request;
    /**
     * @var SubscriptionService
     */
    protected $subscriptionService;

    public function __construct(Request $request)
    {
        $this->request = $request;
        Magento2CoreSetup::bootstrap();
        $this->i18n = new LocalizationService();
        $this->moneyService = new MoneyService();
        $this->subscriptionService = new SubscriptionService();
    }

    /**
     * List product subscription
     *
     * @return mixed
     */
    public function list()
    {
        $result = $this->subscriptionService->listAll();
        return json_decode(json_encode($result), true);
    }

    /**
     * Cancel subscription
     *
     * @param int $id
     * @return mixed
     */
    public function cancel($id)
    {
        try {
            $response = $this->subscriptionService->cancel($id);
            return $response;
        } catch (\Exception $exception) {
            return [
                "code" => $exception->getCode(),
                "message" => $exception->getMessage()
            ];
        }
    }

    /**
     * List product subscription
     *
     * @param string $customerId
     * @return \Mundipagg\Core\Recurrence\Interfaces\SubscriptionInterface[]
     */
    public function listByCustomerId($customerId)
    {
        // TODO: Implement listByCustomerId() method.
    }
}