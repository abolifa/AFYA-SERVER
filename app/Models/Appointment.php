<?php

namespace App\Models;

use App\Models\Traits\BlamesUser;
use App\Models\Traits\ScopeByCenter;
use Database\Factories\AppointmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @method static whereDate(string $string, Carbon $now)
 * @method static min(string $string)
 * @method static max(string $string)
 * @method static where(string $string, mixed $id)
 * @method static count()
 */
class Appointment extends Model
{
    /** @use HasFactory<AppointmentFactory> */
    use HasFactory, SoftDeletes, BlamesUser, ScopeByCenter;

    protected $guarded = ['id'];

    protected $appends = ['is_dirty'];
    

    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function getIsDirtyAttribute(): bool
    {
        $appointmentDateTime = Carbon::parse("$this->date $this->time");
        return $appointmentDateTime->isPast() || in_array($this->status, ['cancelled', 'completed']);
    }
}
