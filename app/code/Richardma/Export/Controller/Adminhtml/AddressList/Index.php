<?php
namespace Richardma\Export\Controller\Adminhtml\AddressList;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
     protected $resultPageFactory;

    /**
     * @var scopeConfig
     * Needed to retrieve config values
     */
    protected $scopeConfig;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
    * Index Action*
    * @return void
    */
    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}
