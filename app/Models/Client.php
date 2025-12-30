<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $is_self
 * @property string $name
 * @property string|null $identifier
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $birthdate
 * @property int|null $height_cm
 * @property float|null $weight_kg
 * @property string|null $phone
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $owner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Test> $tests
 * @property-read int|null $tests_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereBirthdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereHeightCm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereIsSelf($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereWeightKg($value)
 * @mixin \Eloquent
 */
class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'identifier',
        'email',
        'phone',
        'birthdate',
        'height_cm',
        'weight_kg',
        'is_self',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'height_cm' => 'integer',
        'weight_kg' => 'float',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tests()
    {
        return $this->hasMany(Test::class, 'client_id');
    }
}
