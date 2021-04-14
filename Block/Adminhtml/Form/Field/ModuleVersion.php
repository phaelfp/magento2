<?php

namespace MundiPagg\MundiPagg\Block\Adminhtml\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Mundipagg\Core\Kernel\Services\VersionService;
use MundiPagg\MundiPagg\Concrete\Magento2CoreSetup;
use Magento\Config\Block\System\Config\Form\Field;

class ModuleVersion extends Field
{
    /**
     * @param AbstractElement $element
     * @return string
     * @throws \Exception
     */
    protected function _renderValue(AbstractElement $element)
    {
        Magento2CoreSetup::bootstrap();
        $versionService = new VersionService();

        $html = '<td class="value">';
        $html .= $this->_getElementHtml($element);
        $html .= "<p><span>{$versionService->getModuleVersion()}</span></p>";
        $html .= '</td>';

        return $html;
    }
}
