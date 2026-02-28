<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConfigurationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_config_is_loaded(): void
    {
        $this->assertNotNull(config('payment.gateway'));
    }

    public function test_iyzico_config_is_loaded(): void
    {
        $this->assertNotNull(config('payment.iyzico'));
    }

    public function test_stripe_config_is_loaded(): void
    {
        $this->assertNotNull(config('payment.stripe'));
    }

    public function test_app_key_is_generated(): void
    {
        $this->assertNotEmpty(config('app.key'));
    }

    public function test_database_is_configured(): void
    {
        $this->assertNotNull(config('database.default'));
    }

    public function test_cache_is_configured(): void
    {
        $this->assertNotNull(config('cache.default'));
    }

    public function test_queue_is_configured(): void
    {
        $this->assertNotNull(config('queue.default'));
    }

    public function test_mail_config_exists(): void
    {
        $this->assertTrue(config()->has('mail'));
    }

    public function test_logging_is_configured(): void
    {
        $this->assertNotNull(config('logging.default'));
    }

    public function test_session_is_configured(): void
    {
        $this->assertNotNull(config('session.driver'));
    }
}
