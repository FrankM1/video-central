<?php

include("../video-central.php");

class VideoCentralTest extends PHPUnit_Framework_TestCase
{
    public function testGetInstance()
    {
        $this->assertInstanceOf('VideoCentral', VideoCentral::getInstance());
    }
}

new VideoCentralTest;
