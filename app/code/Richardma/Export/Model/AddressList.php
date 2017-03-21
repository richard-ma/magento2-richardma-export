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

class AddressList
{
    protected $directory;
    protected $collectionFactory;

    /**
     * AddressList constructor.
     * @param Filesystem $filesystem
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Filesystem $filesystem,
        CollectionFactory $collectionFactory
    ) {
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function exportAddressList(array $order_ids)
    {
        $name = 'addresslist'.md5(microtime());
        $file = 'export/'.$name.'.csv';

        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();
        // title line
        $stream->writeCsv(['OrderNo','Name','Address','City','Province','Post','Country','Tel']);
        $orderCollection = $this->collectionFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', array('in' => $order_ids))
            ->load();
        foreach ($orderCollection as $order) {
            $address = $order->getShippingAddress();
            $stream->writeCsv([
                $order->getId(),
                $address->getName(),
                implode(' ', $address->getStreet()),
                $address->getCity(),
                $address->getRegion(),
                $address->getPostCode(),
                $address->getCountryId(),
                $address->getTelephone()
            ]);
        }
        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true
        ];
    }
}
