<?php
namespace Bss\ReorderProduct\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Import
 * @package Bss\ReorderProduct\Console
 */
class Import extends Command
{
    /**
     * @var \Bss\ReorderProduct\Model\ResourceModel\Import
     */
    protected $importResourceFactory;

    /**
     * Import constructor.
     *
     * @param \Bss\ReorderProduct\Model\ResourceModel\ImportFactory $importResourceFactory
     * @param string $name
     */
    public function __construct(
        \Bss\ReorderProduct\Model\ResourceModel\ImportFactory $importResourceFactory,
        string $name = null
    ) {
        $this->importResourceFactory = $importResourceFactory;
        parent::__construct($name);
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('bss:reoder-product:import');
        $this->setDescription(__('Import reoder product'));
        $this->addArgument('number', null, __('Limit product each a run.'));
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $modelImport = $this->importResourceFactory->create();
        $numberRowEachrun = $input->getArgument('number') ? $input->getArgument('number') : 5000;
        $modelImport->clear();
        $totalRow = $modelImport->getNumberItem();
        $output->writeln("Total item need import: " . ($totalRow));
        for ($i=0; $i < ceil($totalRow/$numberRowEachrun); $i++) {
            $start = $i*$numberRowEachrun;
            $modelImport->import($start, $numberRowEachrun);
            $totalRowImport = $start + $numberRowEachrun;
            if ($totalRowImport >= $totalRow) {
                $output->writeln("Total item import: " . ($totalRow));
                $output->writeln('Import Success!');
            } else {
                $output->writeln("Total item import: " . ($totalRowImport));
            }
        }
    }
}
