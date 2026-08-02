<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Fila única. Vive en base de datos y no en el .env para que los datos de
 * contacto se puedan cambiar desde el panel sin desplegar.
 */
class SiteSetting extends Model
{
    protected $fillable = [
        'whatsapp',
        'phone',
        'email',
        'instagram',
        'facebook',
        'address',
        'hours',
        'about_intro',
        'about_process',
    ];

    public static function current(): self
    {
        return once(fn () => static::query()->firstOrCreate([], [
            'whatsapp' => config('pluss.whatsapp'),
            'email' => config('pluss.email'),
        ]));
    }

    /** Número normalizado a solo dígitos, que es lo que espera wa.me. */
    public function whatsappNumber(): string
    {
        return preg_replace('/\D+/', '', (string) ($this->whatsapp ?: config('pluss.whatsapp')));
    }

    public function whatsappUrl(string $message = ''): string
    {
        $url = 'https://wa.me/'.$this->whatsappNumber();

        return $message === '' ? $url : $url.'?text='.rawurlencode($message);
    }
}
