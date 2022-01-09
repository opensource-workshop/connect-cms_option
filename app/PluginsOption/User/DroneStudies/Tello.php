<?php

namespace App\PluginsOption\User\Dronestudies;

class Tello
{

    const TELLO_IP = '192.168.10.1';
    const TELLO_PORT = 8889;

    protected $localhost;
    protected $localport;
    protected $socket;
    protected $test_mode;

    protected $recv_strs;

    public function __construct($test_mode = 0, $localhost = '192.168.10.2', $localport = 9000)
    {
        $this->localhost = $localhost;
        $this->localport = $localport;

        $this->recv_strs = array();

        $this->test_mode = $test_mode;

        if ($this->test_mode) {
            return;
        }

        $this->preFlightSetup();
    }

    public function getRecvStrs()
    {
        if ($this->test_mode) {
            return;
        }
        return $this->recv_strs;
    }

    public function takeoff()
    {
        if ($this->test_mode) {
            return;
        }
        return $this->sendCommand('takeoff');
    }

    public function land()
    {
        if ($this->test_mode) {
            return;
        }
        return $this->sendCommand('land');
    }

    public function up($height = 20)
    {
        if ($this->test_mode) {
            return;
        }

        $height = $this->evaluateDistanceParam($height);

        return $this->sendCommand('up ' . $height);
    }

    public function down($height = 20)
    {
        if ($this->test_mode) {
            return;
        }

        $height = $this->evaluateDistanceParam($height);

        return $this->sendCommand('down ' . $height);
    }

    public function left($distance = 20)
    {
        if ($this->test_mode) {
            return;
        }

        $distance = $this->evaluateDistanceParam($distance);

        return $this->sendCommand('left ' . $distance);
    }

    public function right($distance = 20)
    {
        if ($this->test_mode) {
            return;
        }

        $distance = $this->evaluateDistanceParam($distance);

        return $this->sendCommand('right ' . $distance);
    }

    public function forward($distance = 20)
    {
        if ($this->test_mode) {
            return;
        }

        $distance = $this->evaluateDistanceParam($distance);

        return $this->sendCommand('forward ' . $distance);
    }

    public function back($distance = 20)
    {
        if ($this->test_mode) {
            return;
        }

        $distance = $this->evaluateDistanceParam($distance);

        return $this->sendCommand('back ' . $distance);
    }

    public function cw($angle = 1)
    {
        if ($this->test_mode) {
            return;
        }

        $angle = $this->evaluateAngleParam($angle);

        return $this->sendCommand('cw ' . $angle);
    }

    public function ccw($angle = 1)
    {
        if ($this->test_mode) {
            return;
        }

        $angle = $this->evaluateAngleParam($angle);

        return $this->sendCommand('ccw ' . $angle);
    }

    public function flip($direction)
    {
        if ($this->test_mode) {
            return;
        }

        return $this->sendCommand('flip ' . $direction);
    }

    public function streamon()
    {
        if ($this->test_mode) {
            return;
        }
        return $this->sendCommand('streamon');
    }

    public function streamoff()
    {
        if ($this->test_mode) {
            return;
        }
        return $this->sendCommand('streamoff');
    }

    public function setSpeed($speed)
    {
        if ($this->test_mode) {
            return;
        }

        $speed = intval($speed);

        if ($speed < 1) {
            $speed = 1;
        }

        if ($speed > 100) {
            $speed = 100;
        }

        return $this->sendCommand('speed ' . $speed);
    }

    public function readSpeed()
    {
        if ($this->test_mode) {
            return;
        }

        return $this->sendCommand('Speed?');
    }

    public function readBattery()
    {
        if ($this->test_mode) {
            return;
        }

        //return $this->sendCommand('Battery?');
        $this->sendCommand('Battery?');
        return socket_read($this->socket, 1518);
    }

    public function readTof()
    {
        if ($this->test_mode) {
            return;
        }

        $this->sendCommand('tof?');
        // socket_recv($this->socket, $buf, 1518, 0);
        // echo $buf . "<br />\n";
        //return socket_read($this->socket, 1518);

        //$file = 'tello.log';
        //\Log::debug("--- " . date("Y-m-d H:i:s") . " readTof()\n");

        $tof = socket_read($this->socket, 1518);
        //\Log::debug($tof);

        for ( $i = 0; $i < 4; $i++ ) {
            if ( $tof == "ok" ) {
                sleep(3);
                $tof = socket_read($this->socket, 1518);
                //\Log::debug($tof);
            }
            else {
                continue;
            }
        }
        return $tof;
    }

    public function readTime()
    {
        if ($this->test_mode) {
            return;
        }
        return $this->sendCommand('Time?');
    }

    public function preFlightSetup()
    {
        if ($this->test_mode) {
            return;
        }
        $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_bind($this->socket, $this->localhost, $this->localport);
        socket_connect($this->socket, self::TELLO_IP, self::TELLO_PORT);
        $this->sendCommand('command');
    }

    public function endFlight()
    {
        if ($this->test_mode) {
            return;
        }
        $land = $this->land();

        // if land ok, close socket
        if ($land = 1) {
            socket_close($this->socket);
            return true;
        }

        return false;
    }

    protected function sendCommand($command)
    {
        $this->recv_strs[] = $command;

        return socket_send($this->socket, $command, strlen($command), 0);
    }

    protected function evaluateDistanceParam($distance)
    {
        $distance = intval($distance);

        if ($distance < 20) {
            $distance = 20;
        }

        if ($distance > 500) {
            $distance = 500;
        }

        return $distance;
    }

    protected function evaluateAngleParam($angle)
    {
        $angle = intval($angle);

        if ($angle < 1) {
            $angle = 1;
        }

        if ($angle > 360) {
            $angle = 360;
        }

        return $angle;
    }
}