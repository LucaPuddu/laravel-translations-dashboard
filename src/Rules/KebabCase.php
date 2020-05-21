<?php

namespace LPuddu\LaravelTranslationsDashboard\Rules;

use Illuminate\Contracts\Validation\Rule;

class KebabCase implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!is_scalar($value)) {
            return false;
        }

        // Convert value to string
        $value = "$value";

        $matches = [];
        preg_match('/[a-z0-9]+(?:-[a-z0-9]+)*/', $value, $matches);

        return isset($matches[0])
            && strlen($matches[0]) == strlen($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must only contain lowercase letters, numbers or dashes.';
    }
}
