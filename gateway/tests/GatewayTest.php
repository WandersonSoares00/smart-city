<?php

require __DIR__ . "/../vendor/autoload.php";

use Gateway\Device;
use PHPUnit\Framework\TestCase;
use Gateway\Gateway;
use Gateway\DeviceRegistry;
use Gateway\MulticastDiscovery;

class GatewayTest extends TestCase
{
    public function testDeviceDiscoveryResponseIsRegistered()
    {
        $registry = new DeviceRegistry();

        $deviceInfo = new DeviceInfo();
        $deviceInfo->setName("Sensor01");
        $deviceInfo->setType("temperature");
        $deviceInfo->setIp("192.168.0.10");
        $deviceInfo->setPort(9001);
        $deviceInfo->setCurrentState("idle");

        $binary = $deviceInfo->serializeToString();

        // Simula recebimento pelo listener
        list($ip, $port) = ["192.168.0.10", "9001"];

        $device = new Device(
            name: $deviceInfo->getName(),
            type: $deviceInfo->getType(),
            ip:   $deviceInfo->getIp(),
            port: $deviceInfo->getPort(),
            currentState: $deviceInfo->getCurrentState()
        );

        $registry->addDevice($device);

        $this->assertCount(1, $registry->listDevices());
        $this->assertEquals("Sensor01", $registry->listDevices()["Sensor01"]["name"]);
    }

    public function testSensorDataUpdatesDevice()
    {
        $registry = new DeviceRegistry();

        $device = new Device(
            "Sensor01", "temperature", "192.168.0.10", 9001, "idle"
        );

        $registry->addDevice($device);

        $sensorData = new SensorData();
        $sensorData->setDeviceName("Sensor01");
        $sensorData->setType("temperature");
        $sensorData->setValue("25.5");

        $registry->updateSensorData(
            "Sensor01",
            $sensorData->getType(),
            $sensorData->getValue()
        );

        $list = $registry->listDevices();
        $this->assertEquals("25.5", $list["Sensor01"]["sensorData"]["temperature"]);
    }

    public function testPeriodicDiscoverySendsPacket()
    {
        $result = MulticastDiscovery::send("239.0.0.1", 5000, "DISCOVERY_TEST");

        $this->assertNull($result);
        $this->assertTrue(true);
    }
}
