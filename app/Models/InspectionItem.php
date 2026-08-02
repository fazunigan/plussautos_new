<?php

namespace App\Models;

use App\Enums\InspectionCategory;
use App\Enums\InspectionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Un punto revisado del vehículo. Los ítems que no están conformes y traen
 * foto son los "detalles documentados" que se muestran en la ficha: la
 * galería de defectos se deriva de aquí, no es una carga aparte.
 */
class InspectionItem extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\InspectionItemFactory> */
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'vehicle_id',
        'category',
        'label',
        'status',
        'note',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'category' => InspectionCategory::class,
            'status' => InspectionStatus::class,
            'sort_order' => 'integer',
        ];
    }

    /** @return BelongsTo<Vehicle, $this> */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /** Ítems que constituyen un detalle documentado. */
    public function scopeDetails(Builder $query): Builder
    {
        return $query->where('status', '!=', InspectionStatus::Ok->value);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('evidence')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('detail')
            ->fit(Fit::Contain, 1200, 1200)
            ->format('webp')
            ->performOnCollections('evidence');

        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 320, 240)
            ->format('webp')
            ->performOnCollections('evidence');
    }

    public function photoUrl(string $conversion = 'detail'): ?string
    {
        $media = $this->getFirstMedia('evidence');

        return $media?->hasGeneratedConversion($conversion)
            ? $media->getUrl($conversion)
            : $media?->getUrl();
    }

    public function hasPhoto(): bool
    {
        return $this->getFirstMedia('evidence') !== null;
    }

    /**
     * Texto alternativo específico. Sirve al lector de pantalla y a Google
     * por igual, que es la razón por la que no puede ser genérico.
     */
    public function altText(): string
    {
        return trim($this->label.($this->note ? ': '.$this->note : ''));
    }
}
