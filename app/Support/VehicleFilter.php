<?php

namespace App\Support;

use App\Enums\BodyType;
use App\Enums\Fuel;
use App\Enums\Transmission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Traduce los parámetros de la URL del catálogo a una consulta.
 *
 * El estado de los filtros vive en la URL a propósito: así los resultados se
 * pueden compartir por WhatsApp y Google puede indexar las combinaciones.
 */
final class VehicleFilter
{
    /** @var array<string, string> */
    public const SORTS = [
        'recientes' => 'Más recientes',
        'precio_asc' => 'Menor precio',
        'precio_desc' => 'Mayor precio',
        'km_asc' => 'Menor kilometraje',
    ];

    private function __construct(
        public readonly ?string $marca,
        public readonly ?string $modelo,
        public readonly ?int $anioMin,
        public readonly ?int $anioMax,
        public readonly ?int $precioMin,
        public readonly ?int $precioMax,
        public readonly ?int $kmMax,
        public readonly ?Transmission $transmision,
        public readonly ?Fuel $combustible,
        public readonly ?BodyType $carroceria,
        public readonly string $orden,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            marca: self::str($request->query('marca')),
            modelo: self::str($request->query('modelo')),
            anioMin: self::int($request->query('anio_min'), 1950, 2100),
            anioMax: self::int($request->query('anio_max'), 1950, 2100),
            precioMin: self::int($request->query('precio_min'), 0, 10_000_000_000),
            precioMax: self::int($request->query('precio_max'), 0, 10_000_000_000),
            kmMax: self::int($request->query('km_max'), 0, 2_000_000),
            transmision: Transmission::tryFrom((string) $request->query('transmision')),
            combustible: Fuel::tryFrom((string) $request->query('combustible')),
            carroceria: BodyType::tryFrom((string) $request->query('carroceria')),
            orden: array_key_exists((string) $request->query('orden'), self::SORTS)
                ? (string) $request->query('orden')
                : 'recientes',
        );
    }

    /**
     * @param  Builder<\App\Models\Vehicle>  $query
     * @return Builder<\App\Models\Vehicle>
     */
    public function apply(Builder $query): Builder
    {
        $query
            ->when($this->marca, fn (Builder $q, string $slug) => $q->whereHas(
                'brand', fn (Builder $b) => $b->where('slug', $slug)
            ))
            ->when($this->modelo, fn (Builder $q, string $slug) => $q->whereHas(
                'vehicleModel', fn (Builder $m) => $m->where('slug', $slug)
            ))
            ->when($this->anioMin, fn (Builder $q, int $v) => $q->where('year', '>=', $v))
            ->when($this->anioMax, fn (Builder $q, int $v) => $q->where('year', '<=', $v))
            ->when($this->precioMin, fn (Builder $q, int $v) => $q->where('price', '>=', $v))
            ->when($this->precioMax, fn (Builder $q, int $v) => $q->where('price', '<=', $v))
            ->when($this->kmMax, fn (Builder $q, int $v) => $q->where('mileage_km', '<=', $v))
            ->when($this->transmision, fn (Builder $q, Transmission $v) => $q->where('transmission', $v->value))
            ->when($this->combustible, fn (Builder $q, Fuel $v) => $q->where('fuel', $v->value))
            ->when($this->carroceria, fn (Builder $q, BodyType $v) => $q->where('body_type', $v->value));

        return match ($this->orden) {
            'precio_asc' => $query->orderBy('price'),
            'precio_desc' => $query->orderByDesc('price'),
            'km_asc' => $query->orderBy('mileage_km'),
            default => $query->orderByDesc('published_at')->orderByDesc('id'),
        };
    }

    public function isEmpty(): bool
    {
        return $this->activeKeys() === [];
    }

    /**
     * Claves con filtro aplicado, sin contar el orden: el orden no es un
     * filtro y no debe aparecer como pastilla removible.
     *
     * @return list<string>
     */
    public function activeKeys(): array
    {
        return collect([
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'anio_min' => $this->anioMin,
            'anio_max' => $this->anioMax,
            'precio_min' => $this->precioMin,
            'precio_max' => $this->precioMax,
            'km_max' => $this->kmMax,
            'transmision' => $this->transmision,
            'combustible' => $this->combustible,
            'carroceria' => $this->carroceria,
        ])->filter(fn ($value) => $value !== null)->keys()->all();
    }

    /** @return array<string, string|int|null> */
    public function toQuery(): array
    {
        return array_filter([
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'anio_min' => $this->anioMin,
            'anio_max' => $this->anioMax,
            'precio_min' => $this->precioMin,
            'precio_max' => $this->precioMax,
            'km_max' => $this->kmMax,
            'transmision' => $this->transmision?->value,
            'combustible' => $this->combustible?->value,
            'carroceria' => $this->carroceria?->value,
            'orden' => $this->orden === 'recientes' ? null : $this->orden,
        ], fn ($value) => $value !== null);
    }

    private static function str(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : '';

        return $value === '' ? null : $value;
    }

    private static function int(mixed $value, int $min, int $max): ?int
    {
        if (! is_numeric($value)) {
            return null;
        }

        $value = (int) $value;

        return $value >= $min && $value <= $max ? $value : null;
    }
}
