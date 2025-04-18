<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidRegistration implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (strlen($value) !== 11) {
            $fail('A matrícula está incorreta.');
            return;
        }

        $ano = substr($value, 0, 2);
        $primeirosTres = substr($value, 2, 3);
        $tresLetras = substr($value, 5, 3);
        $ultimosTres = substr($value, 8, 3);

        $anoValido = is_numeric($ano) && intval($ano) >= 19 && intval($ano) <= 25;
        $num1Valido = ctype_digit($primeirosTres);
        $letrasValidas = ctype_alpha($tresLetras);
        $num2Valido = ctype_digit($ultimosTres);

        if (!($anoValido && $num1Valido && $letrasValidas && $num2Valido)) {
            $fail('A matrícula está incorreta.');
        }
    }
}
