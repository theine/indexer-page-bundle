<?php

/*
 * This file is part of the phlexible indexer page package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\IndexerPageBundle\Command;

use Phlexible\Bundle\IndexerPageBundle\Indexer\PageIndexer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Add all command.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddAllCommand extends Command
{
    private $indexer;

    public function __construct(PageIndexer $indexer)
    {
        parent::__construct();

        $this->indexer = $indexer;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('indexer-page:add-all')
            ->setDescription('Index all page documents.')
            ->addOption('queue', null, InputOption::VALUE_NONE, 'Queue updates instead of immediate run.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $storage = $this->indexer->getStorage();

        $output->writeln('Indexer: '.get_class($this->indexer));
        $output->writeln('  Storage: '.get_class($storage));
        $output->writeln('    DSN: '.$storage->getConnectionString());

        $viaQueue = $input->getOption('queue');

        if ($viaQueue) {
            $result = $this->indexer->queueAll();
        } else {
            $result = $this->indexer->indexAll();
        }

        if (!$result) {
            $output->writeln('Nothing to index.');
        } else {
            if ($viaQueue) {
                $output->writeln("Queued $result document-adds.");
            } else {
                $output->writeln("Added $result documents to index.");
            }
        }

        return 0;
    }
}
