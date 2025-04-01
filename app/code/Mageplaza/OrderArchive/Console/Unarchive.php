<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  Mageplaza
 * @package   Mageplaza_OrderArchive
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\OrderArchive\Console;

use Exception;
use Mageplaza\OrderArchive\Helper\Data as HelperData;
use Mageplaza\OrderArchive\Model\ResourceModel\Action;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Unarchive
 *
 * @package Mageplaza\OrderArchive\Console
 */
class Unarchive extends Command
{
    const ORDER_ID = 'order_id';

    /**
     * @var Action
     */
    protected $_action;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * Unarchive constructor.
     *
     * @param Action $action
     * @param HelperData $helperData
     * @param null $name
     */
    public function __construct(
        Action $action,
        HelperData $helperData,
        $name = null
    ) {
        $this->_action     = $action;
        $this->_helperData = $helperData;

        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('order:unarchive')
            ->setDescription('Unarchive order by id')
            ->addArgument(self::ORDER_ID, InputArgument::OPTIONAL, __('Order Id'));
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->_helperData->isEnabled()) {
            $output->writeln('<error>Please enable the module.</error>');

            return;
        }

        $orderId = $input->getArgument(self::ORDER_ID);
        if (in_array($orderId, $this->_action->getAllOrderIds(), true)) {
            try {
                $this->_action->unarchiveOrder($orderId);
                $output->writeln('<info>The archive process has been successful!</info>');
            } catch (Exception $e) {
                $output->writeln("<error>{$e->getMessage()}</error>");
            }
        } else {
            $output->writeln('<error>The order ID has not been found!</error>');
        }
    }
}
