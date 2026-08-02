<?php

namespace App\Models;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Enums\VehicleCondition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    /** @use HasFactory<\Database\Factories\LeadFactory> */
    use HasFactory;

    protected $fillable = [
        'type',
        'vehicle_id',
        'name',
        'phone',
        'email',
        'message',
        't_brand',
        't_model',
        't_version',
        't_year',
        't_mileage_km',
        't_condition',
        't_comuna',
        't_plate',
        'status',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'type' => LeadType::class,
            'status' => LeadStatus::class,
            't_condition' => VehicleCondition::class,
            't_year' => 'integer',
            't_mileage_km' => 'integer',
        ];
    }

    /** @return BelongsTo<Vehicle, $this> */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /** Resumen del auto que el dueño quiere vender, para la bandeja del panel. */
    public function appraisalSummary(): ?string
    {
        if ($this->type !== LeadType::Tasacion) {
            return null;
        }

        return collect([$this->t_brand, $this->t_model, $this->t_version, $this->t_year])
            ->filter()
            ->implode(' ') ?: null;
    }

    /** Segunda línea de la bandeja: lo que define la oferta. */
    public function appraisalDetail(): ?string
    {
        if ($this->type !== LeadType::Tasacion) {
            return null;
        }

        return collect([
            $this->t_mileage_km ? number_format($this->t_mileage_km, 0, ',', '.').' km' : null,
            $this->t_condition?->label(),
            $this->t_comuna,
        ])->filter()->implode(' · ') ?: null;
    }
}
