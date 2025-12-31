<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

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

    // Campos asignables
    protected $fillable = [
        'user_id',
        'client_id',
        'subject_age',
        'subject_height_cm',
        'subject_weight_kg',
        'health_challenges',
        'pdf_text',
        'status',
    ];

    // Casts
    protected $casts = [
        'user_id' => 'integer',
        'client_id' => 'integer',
        'subject_age' => 'integer',
        'subject_height_cm' => 'integer',
        'subject_weight_kg' => 'decimal:2',
        'health_challenges' => 'string',
        'pdf_text' => 'string',
    ];

    // Default attributes (mirrors migration default)
    protected $attributes = [
        'status' => 'Registrado',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }


    // Accessor: URL público del PDF (disk "public")
    public function getPdfUrlAttribute(): ?string
    {
        if (empty($this->pdf_text)) {
            return null;
        }
        return Storage::disk('public')->url($this->pdf_text);
    }

    // Helper para almacenar el PDF con nombre "tests/test_{id}.pdf"
    // Retorna la ruta guardada (relativa al disk) o null
    public function storePdf(UploadedFile $file): ?string
    {
        if (!$this->exists) {
            // asegurar que el modelo tenga id
            $this->save();
        }
        $filename = "test_{$this->id}.pdf";
        $path = $file->storeAs('tests', $filename, 'public');
        if ($path) {
            $this->pdf_text = $path;
            $this->save();
            return $path;
        }
        return null;
    }
}
