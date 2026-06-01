<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverBy;

class SystemTest extends TestCase
{
    private $driver;

    private $baseUrl = 'http://localhost:8000';

    protected function setUp(): void
    {
        $host = 'http://localhost:9515';

        $options = new ChromeOptions();

        $options->addArguments([
            '--headless',
            '--disable-gpu',
            '--no-sandbox'
        ]);

        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(
            ChromeOptions::CAPABILITY,
            $options
        );

        $this->driver = RemoteWebDriver::create(
            $host,
            $capabilities
        );
    }

    public function testHomepageLoads()
    {
        $this->driver->get($this->baseUrl);

        $bodyText = $this->driver
            ->findElement(WebDriverBy::tagName('body'))
            ->getText();

        $this->assertStringContainsString(
            'Toko Online',
            $bodyText
        );
    }

    protected function tearDown(): void
    {
        if ($this->driver) {
            $this->driver->quit();
        }
    }
}