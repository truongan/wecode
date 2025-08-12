<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IPranges implements ValidationRule
{
	/**
	 * Run the validation rule.
	 *
	 * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
	 */
	public function validate(string $attribute, mixed $value, Closure $fail): void
	{
		//
		$ranges = preg_split('/\s+/', $value);

		foreach($ranges as $range){
		    if (!preg_match('/^([01]?\d\d?|2[0-4]\d|25[0-5])(?:\.(?:[01]?\d\d?|2[0-4]\d|25[0-5])){3}(\/(?:[0-2]\d|3[0-2]|[0-9]))?$/', $range)){
                $fail("In {$attribute}, the line {$range} is not a valid IP range");
			}
		}
	}
}
