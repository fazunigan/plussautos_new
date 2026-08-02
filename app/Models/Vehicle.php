<?php

namespace App\Models;

use App\Enums\BodyType;
use App\Enums\Fuel;
use App\Enums\InspectionStatus;
use App\Enums\Traction;
use App\Enums\Transmission;
use App\Enums\VehicleOrigin;
use App\Enums\VehicleStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Vehicle extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\VehicleFactory> */
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $guarded = ['id'];

    /**
     * Los campos internos nunca se serializan. Una filtración del precio de
     * compra o de los datos del consignante es un problema comercial serio,
     * así que la protección vive en el modelo y no solo en las vistas.
     *
     * @var list<string>
     */
    protected $hidden = [
        'plate',
        'consignor_name',
        'consignor_phone',
        'purchase_price',
        'commission_amount',
        'location',
        'internal_notes',
    ];

    protected function casts(): array
    {
        return [
            'transmission' => Transmission::class,
            'fuel' => Fuel::class,
            'body_type' => BodyType::class,
            'traction' => Traction::class,
            'status' => VehicleStatus::class,
            'origin' => VehicleOrigin::class,
            'published_at' => 'datetime',
            'sold_at' => 'datetime',
            'featured' => 'boolean',
            'year' => 'integer',
            'price' => 'integer',
            'mileage_km' => 'integer',
            'engine_cc' => 'integer',
            'doors' => 'integer',
            'owners_count' => 'integer',
            'purchase_price' => 'integer',
            'commission_amount' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Relaciones

    /** @return BelongsTo<Brand, $this> */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /** @return BelongsTo<VehicleModel, $this> */
    public function vehicleModel(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class);
    }

    /** @return HasMany<InspectionItem, $this> */
    public function inspectionItems(): HasMany
    {
        return $this->hasMany(InspectionItem::class)->orderBy('sort_order');
    }

    /** @return HasMany<Lead, $this> */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    // Alcances

    /** Vehículos que aparecen listados en el catálogo. */
    public function scopeListable(Builder $query): Builder
    {
        return $query
            ->whereIn('status', array_column(VehicleStatus::listable(), 'value'))
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Vehículos con página pública accesible. Incluye los vendidos: sus URLs
     * ya tienen posicionamiento y sirven de prueba social.
     */
    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->whereIn('status', array_column(VehicleStatus::publiclyVisible(), 'value'))
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    // Atributos derivados

    public function title(): string
    {
        return trim("{$this->brand?->name} {$this->vehicleModel?->name}");
    }

    public function fullTitle(): string
    {
        return collect([$this->brand?->name, $this->vehicleModel?->name, $this->version, $this->year])
            ->filter()
            ->implode(' ');
    }

    public function formattedPrice(): string
    {
        return '$'.number_format($this->price, 0, ',', '.');
    }

    public function formattedMileage(): string
    {
        return number_format($this->mileage_km, 0, ',', '.').' km';
    }

    /**
     * Métrica por la que se maneja una compraventa. Solo para el panel:
     * no se muestra en el sitio público.
     */
    public function daysInStock(): ?int
    {
        if ($this->published_at === null) {
            return null;
        }

        return (int) $this->published_at->diffInDays($this->sold_at ?? now());
    }

    /** @return Collection<int, InspectionItem> */
    public function documentedDetails(): Collection
    {
        return $this->inspectionItems
            ->filter(fn (InspectionItem $item) => $item->status->isDetail())
            ->values();
    }

    public function documentedDetailsCount(): int
    {
        return $this->documentedDetails()->count();
    }

    /**
     * Hoja de inspección agrupada por categoría, en el orden de presentación
     * definido por la categoría.
     *
     * @return Collection<string, Collection<int, InspectionItem>>
     */
    public function inspectionByCategory(): Collection
    {
        return $this->inspectionItems
            ->sortBy(fn (InspectionItem $item) => [$item->category->order(), $item->sort_order])
            ->groupBy(fn (InspectionItem $item) => $item->category->value);
    }

    public function hasInspection(): bool
    {
        return $this->inspectionItems->isNotEmpty();
    }

    // Contacto

    public function whatsappMessage(): string
    {
        return "Hola, me interesa el {$this->fullTitle()} publicado en ".route('vehicles.show', $this);
    }

    public function whatsappUrl(): string
    {
        return SiteSetting::current()->whatsappUrl($this->whatsappMessage());
    }

    // Medios

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 400, 300)
            ->format('webp')
            ->performOnCollections('gallery');

        $this->addMediaConversion('card')
            ->fit(Fit::Crop, 600, 450)
            ->format('webp')
            ->performOnCollections('gallery');

        $this->addMediaConversion('full')
            ->fit(Fit::Contain, 1600, 1200)
            ->format('webp')
            ->performOnCollections('gallery');
    }

    public function gallery(): MediaCollection
    {
        return $this->getMedia('gallery');
    }

    public function coverUrl(string $conversion = 'card'): ?string
    {
        $media = $this->getFirstMedia('gallery');

        if ($media === null) {
            return null;
        }

        return $media->hasGeneratedConversion($conversion)
            ? $media->getUrl($conversion)
            : $media->getUrl();
    }

    /** Texto alternativo descriptivo: accesibilidad y SEO a la vez. */
    public function coverAlt(): string
    {
        return $this->fullTitle().' usado en venta';
    }

    // Slug

    protected static function booted(): void
    {
        // El slug se arma en dos tiempos porque la columna no admite nulos:
        // en el insert va la base, y una vez que existe el id se le agrega.
        // El id garantiza unicidad entre autos del mismo modelo y año, que es
        // el caso corriente en un catálogo.
        static::saving(function (self $vehicle): void {
            if ($vehicle->slug === null) {
                $vehicle->slug = $vehicle->generateSlug().'-'.Str::random(6);
            }
        });

        static::created(function (self $vehicle): void {
            $slug = $vehicle->generateSlug().'-'.$vehicle->id;

            $vehicle->newQuery()->whereKey($vehicle->id)->update(['slug' => $slug]);

            $vehicle->setAttribute('slug', $slug);
            $vehicle->syncOriginalAttribute('slug');
        });
    }

    protected function generateSlug(): string
    {
        return Str::slug(collect([
            $this->brand?->name ?? Brand::find($this->brand_id)?->name,
            $this->vehicleModel?->name ?? VehicleModel::find($this->vehicle_model_id)?->name,
            $this->version,
            $this->year,
        ])->filter()->implode(' '));
    }
}
