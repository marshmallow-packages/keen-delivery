<?php

namespace Marshmallow\KeenDelivery\Models;

use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Delivery extends Model
{
    protected $guarded = [];

    protected $casts = [
        'extra_data' => 'array',
        'payload' => 'array',
        'response' => 'array',
    ];

    public function hasLabel()
    {
        return $this->label_encoded !== null;
    }

    public function downloadLabelRoute()
    {
        return URL::temporarySignedRoute(
            config('keen-delivery.routes.single_label.name'),
            now()->addMinutes(
                config('keen-delivery.routes.single_label.ttl')
            ),
            ['delivery' => $this]
        );
    }

    public function getNovaStatus()
    {
        if ($this->carrier_shipping_id) {
            return __('success');
        }

        return __('has errors');
    }

    public function getLabelName()
    {
        return "{$this->track_and_trace_id}.pdf";
    }

    public function getLabelContent()
    {
        return base64_decode($this->label_encoded);
    }

    public function scopeWhereHasLabel(Builder $builder)
    {
        $builder->whereNotNull('label_encoded');
    }

    public function scopeWhereHasNoLabel(Builder $builder)
    {
        $builder->whereNull('label_encoded');
    }

    public function scopeLegacy(Builder $builder)
    {
        $builder->where('carrier_shipping_id', 'regexp', '^[0-9]+$');
    }

    public function scopeSendy(Builder $builder)
    {
        $builder->whereNot('carrier_shipping_id', 'regexp', '^[0-9]+$');
    }

    public function getIsLegacyAttribute()
    {
        return is_numeric($this->carrier_shipping_id);
    }

    public function deliverable()
    {
        return $this->morphTo();
    }
}
