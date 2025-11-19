<?php
namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class ExistsTwoParams implements ValidationRule
{
    protected string $table;
    protected string $columnA;
    protected mixed $valueA;
    protected string $columnB;
    protected mixed $valueB;

    public function __construct(string $table, string $columnA, mixed $valueA, string $columnB, mixed $valueB)
    {
        $this->table = $table;
        $this->columnA = $columnA;
        $this->valueA = $valueA;
        $this->columnB = $columnB;
        $this->valueB = $valueB;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = DB::table($this->table)
            ->where($this->columnA, $this->valueA)
            ->where($this->columnB, $this->valueB)
            ->exists();

        if (!$exists) {
            $fail("no es válida.");
        }
    }
}