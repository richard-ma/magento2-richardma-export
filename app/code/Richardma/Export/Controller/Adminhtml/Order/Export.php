<?php
/**
 * Created by PhpStorm.
 * User: richardma
 * Date: 3/6/17
 * Time: 9:34 AM
 */

namespace Richardma\Export\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Richardma\Export\Model\OrderList;

class Export extends \Magento\Backend\App\Action
{
    /**
     * @var FileFactory
     */
    protected $fileFactory;

    protected $orderList;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        OrderList $orderList
    ) {
        $this->fileFactory = $fileFactory;
        $this->orderList = $orderList;
        parent::__construct($context);
    }

    /**
     * Index Action*
     * @return void
     */
    public function execute()
    {
        $order_ids = $this->_genOrdersIdList($this->_request->getParam('order_ids'));
        if (count($order_ids) > 0) {
            return $this->fileFactory->create('order.csv', $this->orderList->exportOrder($order_ids), 'var');
        } else {
            return $this->_redirect('Export/order/index');
        }
    }

    /**
     * @param $post_string
     * @return array
     */
    private function _genOrdersIdList($post_string)
    {
        $temp_list = explode(',', $post_string);
        $ret_list = array();
        foreach($temp_list as $id) {
            $id = trim($id);
            if (strpos($id, '-') != false) {
                $range = explode('-', $id);
                $start = $range[0];
                $end = $range[1];
                for ($i = $start; $i <= $end; $i++) {
                    array_push($ret_list, $i);
                }
            } else {
                array_push($ret_list, $id);
            }
        }
        $ret_list = array_unique($ret_list);
        sort($ret_list);

        return $ret_list;
    }
}
