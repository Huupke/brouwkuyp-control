<?php

namespace Brouwkuyp\Bundle\DashboardBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BrewCommand extends ContainerAwareCommand
{
    const SET_TEMP = 'brewery/brewhouse01/masher/set_temp';

    protected function configure()
    {
        $this
            ->setName('brouwkuyp:brew')
            ->setDescription('Start the brewery of beer!')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>We gaan starten met brouwen!</info>');

        $producer = $this->getContainer()->get('old_sound_rabbit_mq.brouwkuyp_producer');
        $setTemp = 62;
        $producer->publish($setTemp, static::SET_TEMP);
        $output->writeln(sprintf("<info>%s:%s</info>", static::SET_TEMP, $setTemp));
    }
}
