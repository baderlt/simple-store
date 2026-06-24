<?php
// app/Models/Banner.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Banner extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'title_ar',
        'description',
        'description_ar',
        'image_path',
        'mobile_image_path',
        'position',
        'cta_text',
        'cta_text_ar',
        'cta_link',
        'order',
        'is_active',
        'start_at',
        'end_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'order' => 'integer',
    ];

    /**
     * Get the image URL.
     */
    public function getImageUrlAttribute()
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }

    /**
     * Get the full image URL.
     */
    public function getFullImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    /**
     * Get the mobile image URL, falling back to the desktop image.
     */
    public function getMobileImageUrlAttribute()
    {
        return $this->mobile_image_path
            ? asset('storage/' . $this->mobile_image_path)
            : $this->full_image_url;
    }

    /**
     * Check if the banner is currently active.
     */
    public function getIsCurrentlyActiveAttribute(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();

        // Check start date
        if ($this->start_at && $this->start_at->gt($now)) {
            return false;
        }

        // Check end date
        if ($this->end_at && $this->end_at->lt($now)) {
            return false;
        }

        return true;
    }

    /**
     * Check if the banner is scheduled for future.
     */
    public function getIsScheduledAttribute(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return $this->start_at && $this->start_at->gt(Carbon::now());
    }

    /**
     * Check if the banner has expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return $this->end_at && $this->end_at->lt(Carbon::now());
    }

    /**
     * Get the status of the banner.
     */
    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        if ($this->is_expired) {
            return 'expired';
        }

        if ($this->is_scheduled) {
            return 'scheduled';
        }

        return 'active';
    }

    /**
     * Get the status label with color.
     */
    public function getStatusLabelAttribute(): array
    {
        $statuses = [
            'active' => ['label' => 'Active', 'color' => 'success', 'icon' => 'play-circle'],
            'inactive' => ['label' => 'Inactive', 'color' => 'secondary', 'icon' => 'pause-circle'],
            'scheduled' => ['label' => 'Programmée', 'color' => 'warning', 'icon' => 'clock'],
            'expired' => ['label' => 'Expirée', 'color' => 'danger', 'icon' => 'calendar-times'],
        ];

        return $statuses[$this->status] ?? $statuses['inactive'];
    }

    /**
     * Get the position label.
     */
    public function getPositionLabelAttribute(): string
    {
        $positions = [
            'hero' => 'Hero (Slider principal)',
            'middle' => 'Milieu de page',
            'bottom' => 'Bas de page',
            'sidebar' => 'Sidebar',
        ];

        return $positions[$this->position] ?? $this->position;
    }

    /**
     * Get the position icon.
     */
    public function getPositionIconAttribute(): string
    {
        $icons = [
            'hero' => 'images',
            'middle' => 'image',
            'bottom' => 'layer-group',
            'sidebar' => 'columns',
        ];

        return $icons[$this->position] ?? 'image';
    }

    public function getLocalizedTitleAttribute(): ?string
    {
        if (app()->getLocale() === 'ar' && filled($this->title_ar)) {
            return $this->title_ar;
        }

        return $this->title;
    }

    public function getLocalizedDescriptionAttribute(): ?string
    {
        if (app()->getLocale() === 'ar' && filled($this->description_ar)) {
            return $this->description_ar;
        }

        return $this->description;
    }

    public function getLocalizedCtaTextAttribute(): ?string
    {
        if (app()->getLocale() === 'ar' && filled($this->cta_text_ar)) {
            return $this->cta_text_ar;
        }

        return $this->cta_text;
    }

    /**
     * Scope a query to only include active banners.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include banners that are currently active.
     */
    public function scopeCurrentlyActive($query)
    {
        $now = Carbon::now();

        return $query->where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('start_at')
                      ->orWhere('start_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('end_at')
                      ->orWhere('end_at', '>=', $now);
            });
    }

    /**
     * Scope a query by position.
     */
    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Scope a query to order by order and created_at.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at', 'desc');
    }

    /**
     * Get banners for a specific position.
     */
    public static function getForPosition($position, $limit = null)
    {
        $query = self::currentlyActive()
            ->byPosition($position)
            ->ordered();

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Get all active banners grouped by position.
     */
    public static function getActiveBannersByPosition()
    {
        return self::currentlyActive()
            ->ordered()
            ->get()
            ->groupBy('position');
    }

    /**
     * Check if banner has a call to action.
     */
    public function hasCta(): bool
    {
        return !empty($this->cta_text) && !empty($this->cta_link);
    }

    /**
     * Activate the banner.
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Deactivate the banner.
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Toggle the active status.
     */
    public function toggleActive(): bool
    {
        return $this->update(['is_active' => !$this->is_active]);
    }

    /**
     * Get the remaining days until expiration.
     */
    public function getRemainingDaysAttribute(): ?int
    {
        if (!$this->end_at) {
            return null;
        }

        return Carbon::now()->diffInDays($this->end_at, false);
    }

    /**
     * Get the days until start.
     */
    public function getDaysUntilStartAttribute(): ?int
    {
        if (!$this->start_at) {
            return null;
        }

        return Carbon::now()->diffInDays($this->start_at, false);
    }

    /**
     * Get human readable time remaining.
     */
    public function getTimeRemainingAttribute(): ?string
    {
        if (!$this->end_at) {
            return 'Pas de date de fin';
        }

        $now = Carbon::now();
        
        if ($this->end_at->lt($now)) {
            return 'Expirée';
        }

        $diff = $now->diff($this->end_at);

        if ($diff->days > 30) {
            return $diff->m . ' mois, ' . $diff->d . ' jours';
        } elseif ($diff->days > 0) {
            return $diff->d . ' jours, ' . $diff->h . ' heures';
        } else {
            return $diff->h . ' heures, ' . $diff->i . ' minutes';
        }
    }

    /**
     * Delete the model from the database.
     */
    public function delete()
    {
        foreach (['image_path', 'mobile_image_path'] as $attribute) {
            if ($this->{$attribute}) {
                Storage::disk('public')->delete($this->{$attribute});
            }
        }

        return parent::delete();
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::deleting(function ($banner) {
            if ($banner->isForceDeleting()) {
                foreach (['image_path', 'mobile_image_path'] as $attribute) {
                    if ($banner->{$attribute}) {
                        Storage::disk('public')->delete($banner->{$attribute});
                    }
                }
            }
        });

        static::updating(function ($banner) {
            foreach (['image_path', 'mobile_image_path'] as $attribute) {
                if ($banner->isDirty($attribute) && $banner->getOriginal($attribute)) {
                    Storage::disk('public')->delete($banner->getOriginal($attribute));
                }
            }
        });
    }
}
