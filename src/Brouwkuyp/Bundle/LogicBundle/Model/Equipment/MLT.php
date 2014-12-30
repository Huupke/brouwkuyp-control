<?php

namespace Brouwkuyp\Bundle\LogicBundle\Model\Equipment;

use Brouwkuyp\Bundle\LogicBundle\Model\Phase;
use Brouwkuyp\Bundle\ServiceBundle\Manager\AMQP\Manager;
use Brouwkuyp\Bundle\ServiceBundle\Manager\BrewControlManagerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class MLT implements EquipmentInterface
{
    /**
     * BrewControlManager
     * @var BrewControlManagerInterface
     */
    private $bcm;

    /**
     * Manager
     * @var Manager
     */
    private $am;

    /**
     * Last Temperature
     * @var integer
     */
    private $lastTemp;

    /**
     *
     * @param BrewControlManagerInterface $bcm
     */
    public function __construct(BrewControlManagerInterface $bcm, Manager $am)
    {
        $this->bcm = $bcm;
        $this->am = $am;
        $this->startReceiving();
    }

    /**
     *
     * @see \Brouwkuyp\Bundle\LogicBundle\Model\Equipment\EquipmentInterface::performTask()
     */
    public function performTask(Phase $phase)
    {
        echo sprintf('MLT::performTask \'%s\': %s', $phase->getType(),
                $phase->getValue()) . PHP_EOL;
        if ($phase->getType() == Phase::CONTROL_TEMP) {
            $this->setHeaterTemperature($phase->getValue());
        } elseif ($phase->getType() == Phase::REACH_TEMP) {
            $this->setHeaterTemperature($phase->getValue());
            if ($this->getCurrentTemperature() > $phase->getValue()) {
                echo "MLT, Temperature reached!" . PHP_EOL;
                $phase->setFinished();
            }
        } elseif ($phase->getType() == Phase::ADD_INGREDIENTS) {
            // TODO: notify UI
            echo sprintf(
                    "Operator, add the following ingredients: '%s'",
                    $phase->getValue()) . PHP_EOL;
        } else {
            throw new \Exception('Unknown Phase type');
        }
    }

    /**
     * Sets the temperature of the Heater Control Module
     *
     * @param float $setpoint
     */
    public function setHeaterTemperature($setpoint)
    {
        $this->bcm->setMashTemperature($setpoint);
    }

    /**
     * Gets the current temperature of the MLT.
     */
    public function getCurrentTemperature()
    {
        $this->am->wait();
        $this->am->receive();
        return $this->lastTemp;
    }

    private function setCurrentTemperature($receivedTemp)
    {
        $this->lastTemp = $receivedTemp;
    }

    private function startReceiving()
    {
        // TODO: refactor want dit hoort niet in logic thuis
        $callback = function (AMQPMessage $msg) {

            $topic = $msg->delivery_info['routing_key'];
            $value = $msg->body;

            // TODO: Hoe kan this hier werken?
            // this is hier een callback
            $this->setCurrentTemperature($value);

            echo sprintf("Message received: %s: %s : %s",
                            (new \DateTime())->format('H:i:s'),
                            $topic,
                            $value
                    ) . PHP_EOL;
        };

        $this->am->consume($callback, 'brewery.brewhouse01.masher.curr_temp');
    }
}
