<?php

namespace Brouwkuyp\Bundle\LogicBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * BrewCommand
 *
 * @author Evert Harmeling <evert.harmeling@freshheads.com>
 */
class BrewCommand extends ContainerAwareCommand
{
    const SET_TEMP = 'brewery.brewhouse01.masher.set_temp';

    protected function configure()
    {
        $this
            ->setName('brouwkuyp:test')
            ->setDescription('Start the brewery of beer!')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
    }
}
