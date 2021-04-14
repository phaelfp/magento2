<?php

namespace MundiPagg\MundiPagg\Ui\Component\Column\Subscriptions;

use Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\UrlInterface;

class Actions extends Column
{
    /** Url path */
    const URL_PATH_EDIT = 'mundipagg_mundipagg/invoices/index';
    const URL_PATH_DELETE = 'mundipagg_mundipagg/subscriptions/delete';
    const URL_PATH_DETAILS = 'mundipagg_mundipagg/subscriptions/details';
    /** @var UrlBuilder */
    protected $actionUrlBuilder;
    /** @var UrlInterface */
    protected $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlBuilder $actionUrlBuilder
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlBuilder $actionUrlBuilder,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->actionUrlBuilder = $actionUrlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                $actions = $this->getActions($name, $item);
                $item = array_merge($item, $actions);

            }
        }
        return $dataSource;
    }

    protected function getActions($name, $item)
    {
        $actions[$name]['edit'] = [
            'href' => $this->getUrlMundipaggEdit($item, self::URL_PATH_EDIT),
            'label' => __('Invoices')
        ];
        $actions[$name]['details'] = [
            'href' => $this->getUrlMundipaggEdit($item, self::URL_PATH_DETAILS),
            'label' => __('Subscription details')
        ];

        if ($item['status']->getText() != 'canceled') {
            $actions[$name]['delete'] = [
                'href' => $this->getUrlMundipaggDelete($item, self::URL_PATH_DELETE),
                'label' => __('Disable'),
                'confirm' => [
                    'title' => __('Confirm action'),
                    'message' => __('Are you sure you want to disabled this item?')
                ]
            ];
        }


        return $actions;
    }

    protected function getUrlMundipaggEdit($item, $path)
    {
        $url = $this->urlBuilder->getUrl($path);
        return $url . "?subscription_id={$item['mundipagg_id']}";
    }

    protected function getUrlMundipaggDetails($item, $path)
    {
        $url = $this->urlBuilder->getUrl($path);
        return $url . "?subscription_id={$item['mundipagg_id']}";
    }

    protected function getUrlMundipaggDelete($item, $path)
    {
        return $this->urlBuilder->getUrl($path, ['id' => $item['id']]);
    }
}
