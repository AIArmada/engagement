<?php

declare(strict_types=1);

namespace AIArmada\Engagement\Models;

use AIArmada\Engagement\Models\Concerns\UsesEngagementUuid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $id
 * @property string $owner_type
 * @property string $owner_id
 * @property string $name
 * @property string|null $slug
 * @property string|null $description
 * @property string $visibility
 * @property string $status
 * @property int $sort_order
 * @property bool $is_default
 * @property bool $is_system
 * @property \Carbon\CarbonImmutable|null $archived_at
 * @property array|null $metadata
 * @property \Carbon\CarbonImmutable $created_at
 * @property \Carbon\CarbonImmutable $updated_at
 * @property-read Collection<int, BookmarkCollectionItem> $items
 */
final class BookmarkCollection extends Model
{
    use UsesEngagementUuid;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_ARCHIVED = 'archived';
    public const STATUS_LOCKED = 'locked';
    public const VISIBILITY_PRIVATE = 'private';
    public const VISIBILITY_UNLISTED = 'unlisted';
    public const VISIBILITY_PUBLIC = 'public';

    protected $fillable = [
        'owner_type',
        'owner_id',
        'name',
        'slug',
        'description',
        'visibility',
        'status',
        'sort_order',
        'is_default',
        'is_system',
        'metadata',
    ];

    public function getTable(): string
    {
        return config('engagement.database.tables.bookmark_collections', 'bookmark_collections');
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return HasMany<BookmarkCollectionItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(BookmarkCollectionItem::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_system' => 'boolean',
            'metadata' => 'array',
            'archived_at' => 'immutable_datetime',
        ];
    }
}
