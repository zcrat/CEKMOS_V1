<?php

namespace App\Rules;

use App\Models\Tipos;
use App\Models\Categorias as CategoriasModel;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ExistTipo implements ValidationRule
{
    protected string $tipo;
    protected mixed $categoria;

    public function __construct(mixed $categoria, mixed $tipo)
    {
        $this->categoria = $categoria;
        $this->tipo = $tipo;
    }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(Tipos::where('id',$this->tipo)->where('categoria_id',$this->categoria)->doesntExist()){
            $categoria=CategoriasModel::find($this->categoria); 
            $fail('El tipo' .($categoria?' de '.$categoria->descripcion:' '  ).'no existe');
        }
    }
}
