<?php

declare(strict_types=1);

namespace AIArmada\Engagement\Console\Commands;

use AIArmada\Engagement\Contracts\SubscriptionManager;
use Illuminate\Console\Command;

final class MatchSubscriptionsCommand extends Command
{
    protected $signature = 'engagement:match-subscriptions
                            {--trigger= : The trigger event name}';

    protected $description = 'Match subscriptions to subjects';

    public function __construct(
        private readonly SubscriptionManager $subscriptionManager,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $trigger = $this->option('trigger');

        if ($trigger === null) {
            $this->error('The --trigger option is required.');

            return self::FAILURE;
        }

        $this->subscriptionManager->matchingSubscriptions($this, $trigger);

        $this->info("Processed subscription matches for trigger: {$trigger}");

        return self::SUCCESS;
    }
}
