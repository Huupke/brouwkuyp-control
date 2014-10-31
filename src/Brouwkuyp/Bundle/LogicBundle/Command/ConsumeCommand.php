<?php

namespace Brouwkuyp\Bundle\LogicBundle\Command;

use Brouwkuyp\Bundle\ServiceBundle\Entity\Log;
use Brouwkuyp\Bundle\ServiceBundle\Manager\AMQP\Manager;
use Doctrine\ORM\EntityManager;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ConsumeCommand
 *
 * @author Evert Harmeling <evertharmeling@gmail.com>
 */
class ConsumeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('brouwkuyp:consume')
            ->setDescription('Consuming the data')
        ;
    }

    /**
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $output->writeln('<info>We are gonna receive messages!</info>');

        /** @var Manager $manager */
        $manager = $this->getContainer()->get('brouwkuyp_service.amqp.manager');
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        list($queueName) = $channel->queue_declare('', false, false, true, false);

        // listen to all in brewery.#
        $channel->queue_bind($queueName, 'amq.topic', 'brewery.#');

        $callback = function (AMQPMessage $msg) use ($output, $em) {

//            $log = new Log();
//            $log
//                ->setTopic($msg->delivery_info['routing_key'])
//                ->setValue($msg->body)
//            ;
//            $em->persist($log);
//            $em->flush();

            $output->writeln(sprintf("<info>Message received: </info> %s : %s", $msg->delivery_info['routing_key'], $msg->body));
        };

        $manager->consume($callback, 'brewery.#');

        while ($manager->receive()) {
            $manager->wait();
        }

        $manager->close();
    }
}
