<?php

declare(strict_types=1);

namespace AIArmada\Engagement;

use AIArmada\Engagement\Contracts\EngagementCounterService;
use AIArmada\Engagement\Contracts\EngagementManager;
use AIArmada\Engagement\Contracts\EngagementPolicyResolver;
use AIArmada\Engagement\Contracts\EngagementStateResolver;
use AIArmada\Engagement\Contracts\ReminderManager;
use AIArmada\Engagement\Contracts\ShareUrlGenerator;
use AIArmada\Engagement\Contracts\SubscriptionManager;
use AIArmada\Engagement\Services\DefaultEngagementCounterService;
use AIArmada\Engagement\Services\DefaultEngagementManager;
use AIArmada\Engagement\Services\DefaultEngagementPolicyResolver;
use AIArmada\Engagement\Services\DefaultEngagementStateResolver;
use AIArmada\Engagement\Services\DefaultReminderManager;
use AIArmada\Engagement\Services\DefaultShareUrlGenerator;
use AIArmada\Engagement\Services\DefaultSubscriptionManager;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class EngagementServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('engagement')
            ->hasConfigFile()
            ->runsMigrations()
            ->discoversMigrations();
    }

    public function registeringPackage(): void
    {
        $this->app->singleton('engagement');

        $this->app->bind(EngagementManager::class, DefaultEngagementManager::class);
        $this->app->bind(SubscriptionManager::class, DefaultSubscriptionManager::class);
        $this->app->bind(ReminderManager::class, DefaultReminderManager::class);
        $this->app->bind(EngagementStateResolver::class, DefaultEngagementStateResolver::class);
        $this->app->bind(EngagementCounterService::class, DefaultEngagementCounterService::class);
        $this->app->bind(EngagementPolicyResolver::class, DefaultEngagementPolicyResolver::class);
        $this->app->bind(ShareUrlGenerator::class, DefaultShareUrlGenerator::class);

        $this->registerEventsIntegration();
    }

    private function registerEventsIntegration(): void
    {
        if (! class_exists(\AIArmada\Events\EventsServiceProvider::class)) {
            return;
        }

        if (! interface_exists(\AIArmada\Events\Contracts\EventEngagementManager::class)) {
            return;
        }

        $this->app->bind(
            \AIArmada\Events\Contracts\EventEngagementManager::class,
            \AIArmada\Engagement\Integrations\Events\EngagementEventEngagementManager::class,
        );
    }
}
