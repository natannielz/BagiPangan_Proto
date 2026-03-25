<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Collection;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;

abstract class DuskTestCase extends BaseTestCase
{
    /**
     * Prepare for Dusk test execution.
     */
    #[BeforeClass]
    public static function prepare(): void
    {
        if (! static::runningInSail()) {
            static::startChromeDriver(['--port=9515']);
        }

        static::$baseUrl = env('APP_URL', 'http://127.0.0.1:8000');
    }

    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            '--window-size=1280,900',
            '--disable-search-engine-choice-screen',
            '--disable-smooth-scrolling',
            '--no-sandbox',
        ])->unless($this->hasHeadlessDisabled(), function (Collection $items) {
            return $items->merge([
                '--disable-gpu',
                '--headless',
            ]);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? env('DUSK_DRIVER_URL') ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }
}
