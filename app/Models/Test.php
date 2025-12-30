<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $entered_by_id
 * @property int|null $subject_user_id
 * @property int|null $client_id
 * @property bool $is_my_test
 * @property \Illuminate\Support\Carbon|null $sample_date
 * @property string|null $type
 * @property array<array-key, mixed>|null $data
 * @property numeric|null $score
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Client|null $client
 * @property-read \App\Models\User $enteredBy
 * @property-read \App\Models\User|null $subjectUser
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Test newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Test newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Test query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Test whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Test whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Test whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Test whereEnteredById($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Test whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Test whereIsMyTest($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Test whereSampleDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Test whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Test whereSubjectUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Test whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Test whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'entered_by_id',
        'subject_user_id',
        'client_id',
        'is_my_test',
        'sample_date',
        'type',
        'data',
        'score',
        'subject_age',
        'subject_height_cm',
        'subject_weight_kg',
        'health_challenges',
    ];

    protected $casts = [
        'data' => 'array',
        'is_my_test' => 'boolean',
        'sample_date' => 'date',
        'subject_weight_kg' => 'float',
    ];

    public function enteredBy()
    {
        return $this->belongsTo(User::class, 'entered_by_id');
    }

    public function subjectUser()
    {
        return $this->belongsTo(User::class, 'subject_user_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
