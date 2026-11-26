<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CreativeListing extends Model
{
    protected $fillable = ['kind', 'name', 'category', 'village', 'description', 'price', 'whatsapp', 'published', 'photo_path', 'latitude', 'longitude', 'business_id'];

    protected function casts(): array
    {
        return ['published' => 'boolean', 'price' => 'integer'];
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('published', true);
    }

    public function whatsappUrl(): ?string
    {
        return $this->whatsapp && preg_match('/^[1-9][0-9]{7,14}$/D', $this->whatsapp)
            ? 'https://wa.me/'.$this->whatsapp.'?text='.rawurlencode('Halo, saya melihat '.$this->name.' di katalog Ekraf Maniis. Boleh meminta informasi lebih lanjut?') : null;
    }
}
