<?php
/**
 * Class Billet
 *
 * @author      MundiPagg Embeddables Team <embeddables@mundipagg.com>
 * @copyright   2017 MundiPagg (http://www.mundipagg.com)
 * @license     http://www.mundipagg.com Copyright
 *
 * @link        http://www.mundipagg.com
 */

namespace MundiPagg\MundiPagg\Block\Payment\Info;


use Magento\Payment\Block\Info;
use Magento\Framework\DataObject;
use Mundipagg\Core\Kernel\Repositories\OrderRepository;
use Mundipagg\Core\Kernel\Services\OrderService;
use Mundipagg\Core\Kernel\ValueObjects\Id\OrderId;
use Mundipagg\Core\Kernel\ValueObjects\Id\SubscriptionId;
use Mundipagg\Core\Recurrence\Repositories\ChargeRepository as SubscriptionChargeRepository;
use Mundipagg\Core\Recurrence\Repositories\SubscriptionRepository;
use MundiPagg\MundiPagg\Concrete\Magento2CoreSetup;
use MundiPagg\MundiPagg\Concrete\Magento2PlatformOrderDecorator;

class Billet extends Info
{
    const TEMPLATE = 'MundiPagg_MundiPagg::info/billet.phtml';

    public function _construct()
    {
        $this->setTemplate(self::TEMPLATE);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        $transport = new DataObject([
            (string)__('Print Billet') => $this->getBilletUrl()
        ]);

        $transport = parent::_prepareSpecificInformation($transport);
        return $transport;
    }

    public function getBilletUrl()
    {
        $method = $this->getInfo()->getMethod();

        if (strpos($method, "mundipagg_billet") === false) {
            return;
        }

        $boletoUrl =  $this->getInfo()->getAdditionalInformation('billet_url');

        Magento2CoreSetup::bootstrap();
        $info = $this->getInfo();

        $boletoUrl = $this->getBoletoLinkFromOrder($info);

        if (!$boletoUrl) {
            $boletoUrl = $this->getBoletoLinkFromSubscription($info);
        }

        return $boletoUrl;
    }

    private function getBoletoLinkFromOrder($info)
    {
        $lastTransId = $info->getLastTransId();
        $orderId = substr($lastTransId, 0, 19);

        if (!$orderId) {
            return null;
        }

        $orderRepository = new OrderRepository();
        $order = $orderRepository->findByMundipaggId(new OrderId($orderId));

        if ($order !== null) {
            $charges = $order->getCharges();
            foreach ($charges as $charge) {
                $transaction = $charge->getLastTransaction();
                $savedBoletoUrl = $transaction->getBoletoUrl();
                if ($savedBoletoUrl !== null) {
                    $boletoUrl = $savedBoletoUrl;
                }
            }
        }

        return $boletoUrl;
    }

    private function getBoletoLinkFromSubscription($info)
    {
        $subscriptionRepository = new SubscriptionRepository();
        $subscription = $subscriptionRepository->findByCode($info->getOrder()->getIncrementId());

        if (!$subscription) {
            return null;
        }

        $chargeRepository = new SubscriptionChargeRepository();
        $subscriptionId =
            new SubscriptionId(
                $subscription->getMundipaggId()->getValue()
            );

        $charge = $chargeRepository->findBySubscriptionId($subscriptionId);

        if (!empty($charge[0])) {
            return $charge[0]->getBoletoLink();
        }
    }

    public function getTitle()
    {
        return $this->getInfo()->getAdditionalInformation('method_title');
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Mundipagg\Core\Kernel\Exceptions\InvalidParamException
     */
    public function getTransactionInfo()
    {
        Magento2CoreSetup::bootstrap();
        $orderService = new OrderService();

        $orderEntityId = $this->getInfo()->getOrder()->getIncrementId();

        $platformOrder = new Magento2PlatformOrderDecorator();
        $platformOrder->loadByIncrementId($orderEntityId);

        $orderMundipaggId = $platformOrder->getMundipaggId();

        if ($orderMundipaggId === null) {
            return [];
        }

        /**
         * @var \Mundipagg\Core\Kernel\Aggregates\Order orderObject
         */
        $orderObject = $orderService->getOrderByMundiPaggId(new OrderId($orderMundipaggId));
        return $orderObject->getCharges()[0]->getLastTransaction();
    }
}