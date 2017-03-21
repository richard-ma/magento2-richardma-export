<?php
/**
 * Created by PhpStorm.
 * User: richardma
 * Date: 3/9/17
 * Time: 1:57 PM
 */

namespace Richardma\Export\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class OrderList
{
    protected $directory;
    protected $collectionFactory;
    protected $storeManager;

    /**
     * AddressList constructor.
     * @param Filesystem $filesystem
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Filesystem $filesystem,
        CollectionFactory $collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @return array
     */
    public function exportOrder(array $order_ids)
    {
        $orderCollection = $this->collectionFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', array('in' => $order_ids))
            ->load();

        $name = 'addresslist'.md5(microtime());
        $file = 'export/'.$name.'.csv';

        // export Excel xml
        // Include PHPExcel
        require_once(BP."/lib/internal/PHPExcel/Classes/PHPExcel.php");

        //Create new PHPExcel object
        $objPHPExcel = new \PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);

        // Add some data
        $delta = 6;
        $start = 1;
        foreach ($orderCollection as $_order) {
            $address = $_order->getShippingAddress();
            $items = $_order->getAllItems();
            foreach ($items as $_item) {
                $end = $start + $delta - 1;
                $objPHPExcel->getActiveSheet()
                    ->mergeCells('A'.$start.':A'.$end.'')
                    ->setCellValue('A'.$start.'', $_order->getId())

                    ->setCellValue('B'.$start.'', 'size: ' . 'Todo: add size option')//$_item->getProductOptions()['options'][0]['value'])
                    ->mergeCells('B'.(string)($start+1).':B'.$end.'')

                    ->setCellValue('C'.$start.'', $address->getName())
                    ->setCellValue('C'.(string)($start + 1).'', $address->getStreet()[0])
                    ->setCellValue('C'.(string)($start + 2).'', '')//$address->getStreet()[0])
                    ->setCellValue('C'.(string)($start + 3).'', $address->getCity(). ', ' .$address->getRegion(). ' ' .$address->getPostCode())
                    ->setCellValue('C'.(string)($start + 4).'', $address->getCountryId())
                    ->setCellValue('C'.(string)($start + 5).'', $address->getTelephone())

                    ->setCellValue('D'.$start.'', $_item->getName())
                    ->setCellValue('D'.(string)($start + 1).'', 'Qty: '.$_item->getQtyOrdered())
                    ->setCellValue('D'.(string)($start + 2).'', 'SKU: '.$_item->getSku())

                    ->getStyle('C'.(string)($start + 5))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                // add picture
                $objDrawing = new \PHPExcel_Worksheet_Drawing();
                $imageUrl = $_item->getProduct()->getImage();
                $imageUrl = BP . '/pub/media/catalog/product' . $imageUrl;
                $objDrawing->setPath($imageUrl);
                $objDrawing->setCoordinates('B'.(string)($start+1));
                $objDrawing->setHeight(80);
                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

                $start = $start + $delta;
            }
        }

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Orders');

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="orders-'.date('Y_m_d_H_i_s').'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        die();
    }
}
