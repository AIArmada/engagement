<?php

declare(strict_types=1);

$tablePrefix = env('ENGAGEMENT_TABLE_PREFIX', 'engagement_');

return [

    /* Database */
    'database' => [
        'table_prefix' => $tablePrefix,
        'json_column_type' => env('ENGAGEMENT_JSON_COLUMN_TYPE', env('COMMERCE_JSON_COLUMN_TYPE', 'jsonb')),
        'tables' => [
            'follows' => env('ENGAGEMENT_TABLE_FOLLOWS', $tablePrefix . 'follows'),
            'bookmarks' => env('ENGAGEMENT_TABLE_BOOKMARKS', $tablePrefix . 'bookmarks'),
            'bookmark_collections' => env('ENGAGEMENT_TABLE_BOOKMARK_COLLECTIONS', $tablePrefix . 'bookmark_collections'),
            'bookmark_collection_items' => env('ENGAGEMENT_TABLE_BOOKMARK_COLLECTION_ITEMS', $tablePrefix . 'bookmark_collection_items'),
            'responses' => env('ENGAGEMENT_TABLE_RESPONSES', $tablePrefix . 'responses'),
            'reactions' => env('ENGAGEMENT_TABLE_REACTIONS', $tablePrefix . 'reactions'),
            'subscriptions' => env('ENGAGEMENT_TABLE_SUBSCRIPTIONS', $tablePrefix . 'subscriptions'),
            'reminders' => env('ENGAGEMENT_TABLE_REMINDERS', $tablePrefix . 'reminders'),
            'shares' => env('ENGAGEMENT_TABLE_SHARES', $tablePrefix . 'shares'),
            'engagement_counters' => env('ENGAGEMENT_TABLE_COUNTERS', $tablePrefix . 'engagement_counters'),
        ],
    ],

    /* Defaults */
    'defaults' => [
        'follow_notification_level' => env('ENGAGEMENT_DEFAULT_FOLLOW_NOTIFICATION_LEVEL', 'all'),
        'response_visibility' => env('ENGAGEMENT_DEFAULT_RESPONSE_VISIBILITY', 'public'),
    ],

    /* Reminder */
    'reminder' => [
        'batch_size' => (int) env('ENGAGEMENT_REMINDER_BATCH_SIZE', 100),
        'default_channels' => ['mail', 'database'],
    ],

    /* Subscriptions */
    'subscriptions' => [
        'matching_batch_size' => (int) env('ENGAGEMENT_SUBSCRIPTION_MATCHING_BATCH_SIZE', 100),
    ],

    /* Notifications */
    'notifications' => [
        'reminder' => env('ENGAGEMENT_NOTIFICATION_REMINDER_CLASS', \AIArmada\Engagement\Notifications\EngagementReminderNotification::class),
    ],

    /* Model class overrides */
    'models' => [
        'follow' => AIArmada\Engagement\Models\Follow::class,
        'bookmark' => AIArmada\Engagement\Models\Bookmark::class,
        'bookmark_collection' => AIArmada\Engagement\Models\BookmarkCollection::class,
        'bookmark_collection_item' => AIArmada\Engagement\Models\BookmarkCollectionItem::class,
        'response' => AIArmada\Engagement\Models\Response::class,
        'reaction' => AIArmada\Engagement\Models\Reaction::class,
        'subscription' => AIArmada\Engagement\Models\Subscription::class,
        'reminder' => AIArmada\Engagement\Models\Reminder::class,
        'share' => AIArmada\Engagement\Models\Share::class,
        'interaction_counter' => AIArmada\Engagement\Models\InteractionCounter::class,
    ],
];
