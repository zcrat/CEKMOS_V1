<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class TipoCategoriaRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    protected int $categoriaId;

    public function __construct(int $categoriaId)
    {
        $this->categoriaId = $categoriaId;
    }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = DB::table('tipos')
            ->where('id', $value)
            ->where('categoria_id', $this->categoriaId)
            ->exists();

        if (! $exists) {
            $fail("El {$attribute} no pertenece a la categoría correspondiente.");
        }
    }
}
