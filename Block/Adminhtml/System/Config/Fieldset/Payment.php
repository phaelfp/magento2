<?php
/**
 * Class Payment
 *
 * @author      MundiPagg Embeddables Team <embeddables@mundipagg.com>
 * @copyright   2017 MundiPagg (http://www.mundipagg.com)
 * @license     http://www.mundipagg.com Copyright
 *
 * @link        http://www.mundipagg.com
 */

namespace MundiPagg\MundiPagg\Block\Adminhtml\System\Config\Fieldset;

use Magento\Config\Block\System\Config\Form\Fieldset;

class Payment extends Fieldset
{
    /**
     * {@inheritdoc}
     */
    protected function _getFrontendClass($element)
    {
        return parent::_getFrontendClass($element) . ' with-button';
    }

    /**
     * {@inheritdoc}
     */
    protected function _getHeaderTitleHtml($element)
    {
        $legend = $element->getLegend();
        $htmlId = $element->getHtmlId();
        $urlSite = $this->getUrl('*/*/state');
        $configure = __('Configure');
        $close = __('Close');

        $html = '<div class="config-heading">
                <div class="heading"><strong id="logo">{{LEGEND}}</strong></div>
                <div class="button-container">
                    <button type="button" class="action-configure button"
                            id="{{HTML_ID}}-head"
                            onclick="Fieldset.toggleCollapse(\'{{HTML_ID}}\', \'{{URL_SITE}}\'); return false;">
                        <span class="state-closed">{{CONFIGURE}}</span><span
                                class="state-opened">{{CLOSE}}</span></button>
                </div>
              </div>';

        $htmlInterpolated = str_replace(
            ['{{LEGEND}}', '{{HTML_ID}}', '{{URL_SITE}}', '{{CONFIGURE}}', '{{CLOSE}}'],
            [$legend, $htmlId, $urlSite, $configure, $close],
            $html
        );

        return $htmlInterpolated;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getHeaderCommentHtml($element)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    protected function _isCollapseState($element)
    {
        return false;
    }
}
