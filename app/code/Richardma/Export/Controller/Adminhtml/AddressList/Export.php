<?php
/**
 * Created by PhpStorm.
 * User: richardma
 * Date: 3/6/17
 * Time: 9:34 AM
 */

namespace Richardma\Export\Controller\Adminhtml\AddressList;

use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Richardma\Export\Model\AddressList;

class Export extends \Magento\Backend\App\Action
{
    /**
     * @var FileFactory
     */
    protected $fileFactory;

    protected $addresslist;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        AddressList $addresslist
    ) {
        $this->fileFactory = $fileFactory;
        $this->addresslist = $addresslist;
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
            return $this->fileFactory->create('addresslist.csv', $this->addresslist->exportAddressList($order_ids), 'var');
        } else {
            return $this->_redirect('Export/addresslist/index');
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
