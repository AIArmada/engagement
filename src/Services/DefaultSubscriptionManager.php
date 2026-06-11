<?php

declare(strict_types=1);

namespace AIArmada\Engagement\Services;

use AIArmada\Engagement\Contracts\EngagementPolicyResolver;
use AIArmada\Engagement\Contracts\SubscriptionManager;
use AIArmada\Engagement\Events\SubscriptionCancelled;
use AIArmada\Engagement\Events\SubscriptionCreated;
use AIArmada\Engagement\Events\SubscriptionMatched;
use AIArmada\Engagement\Events\SubscriptionMuted;
use AIArmada\Engagement\Events\SubscriptionUnmuted;
use AIArmada\Engagement\Models\Subscription;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

final class DefaultSubscriptionManager implements SubscriptionManager
{
    public function __construct(
        private readonly EngagementPolicyResolver $policy,
    ) {}

    public function subscribe(mixed $subscriber, mixed $subject = null, string $subscriptionType = 'updates', array $criteria = [], array $options = []): Subscription
    {
        $this->policy->canSubscribe($subscriber, $subject, $subscriptionType);

        $query = Subscription::query()
            ->where('subscriber_type', $subscriber->getMorphClass())
            ->where('subscriber_id', $subscriber->getKey())
            ->where('subscription_type', $subscriptionType);

        if ($subject !== null) {
            $query
                ->where('subscribable_type', $subject->getMorphClass())
                ->where('subscribable_id', $subject->getKey());
        } else {
                $query->whereNull('subscribable_type')
                    ->whereNull('subscribable_id');
            }

        $existing = $query->first();

        if ($existing && $existing->status === 'active') {
            return $existing;
        }

        if ($existing) {
            $existing->update([
                'status' => 'active',
                'criteria' => $criteria,
                'unsubscribed_at' => null,
                'subscribed_at' => CarbonImmutable::now(),
                'source' => $options['source'] ?? null,
                'metadata' => $options['metadata'] ?? null,
            ]);
            event(new SubscriptionCreated($existing));

            return $existing;
        }

        $subscription = Subscription::query()->create([
            'subscriber_type' => $subscriber->getMorphClass(),
            'subscriber_id' => $subscriber->getKey(),
            'subscribable_type' => $subject?->getMorphClass(),
            'subscribable_id' => $subject?->getKey(),
            'subscription_type' => $subscriptionType,
            'criteria' => $criteria,
            'status' => 'active',
            'notification_level' => $options['notification_level'] ?? null,
            'subscribed_at' => CarbonImmutable::now(),
            'source' => $options['source'] ?? null,
            'metadata' => $options['metadata'] ?? null,
        ]);

        event(new SubscriptionCreated($subscription));

        return $subscription;
    }

    public function unsubscribe(mixed $subscriber, mixed $subject = null, string $subscriptionType = 'updates', array $criteria = []): void
    {
        $query = Subscription::query()
            ->where('subscriber_type', $subscriber->getMorphClass())
            ->where('subscriber_id', $subscriber->getKey())
            ->where('subscription_type', $subscriptionType)
            ->where('status', 'active');

        if ($subject !== null) {
            $query
                ->where('subscribable_type', $subject->getMorphClass())
                ->where('subscribable_id', $subject->getKey());
        } else {
                $query->whereNull('subscribable_type')
                    ->whereNull('subscribable_id');
            }

        $subscription = $query->first();

        if ($subscription) {
            $subscription->update(['status' => 'unsubscribed', 'unsubscribed_at' => CarbonImmutable::now()]);
            event(new SubscriptionCancelled($subscription));
        }
    }

    public function muteSubscription(Subscription $subscription): Subscription
    {
        $subscription->update(['status' => 'muted', 'muted_at' => CarbonImmutable::now()]);
        event(new SubscriptionMuted($subscription));

        return $subscription;
    }

    public function matchingSubscriptions(mixed $subject, string $trigger, array $context = []): iterable
    {
        $subjectType = $subject->getMorphClass();
        $subjectId = $subject->getKey();

        $subscriptions = Subscription::query()
            ->where('status', 'active')
            ->where(function (Builder $query) use ($subjectType, $subjectId): void {
                $query
                    ->where(function (Builder $q) use ($subjectType, $subjectId): void {
                        $q->where('subscribable_type', $subjectType)
                            ->where('subscribable_id', $subjectId);
                    })
                    ->orWhere(function (Builder $q): void {
                        $q->whereNull('subscribable_type')
                            ->whereNull('subscribable_id');
                    });
            })
            ->cursor();

        foreach ($subscriptions as $subscription) {
            event(new SubscriptionMatched($subscription, $subject, $trigger));

            yield $subscription;
        }
    }
}
