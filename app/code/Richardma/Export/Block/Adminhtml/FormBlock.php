<?php
/**
 * Created by PhpStorm.
 * User: richardma
 * Date: 3/3/17
 * Time: 10:56 AM
 */
namespace Richardma\Export\Block\Adminhtml;

use Magento\Backend\Block\Template;


class FormBlock extends Template
{
    protected $request;

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->request = $request;
        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getControllerName()
    {
        return $this->request->getControllerName();
    }

    public function getFormActionUrl()
    {
        return $this->getUrl('Export/'.$this->request->getControllerName().'/export');
    }
}
