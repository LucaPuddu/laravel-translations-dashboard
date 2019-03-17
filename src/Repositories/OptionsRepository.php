<?php
/**
 * Created by PhpStorm.
 * User: Luca
 * Date: 17/03/2019
 * Time: 00:08
 */

namespace LPuddu\LaravelTranslationsDashboard\Repositories;

use Illuminate\Support\Facades\Validator;
use LPuddu\LaravelTranslationsDashboard\Models\Option;
use stdClass;

class OptionsRepository
{
    private $errors;

    public function get(string $name){
        $option = Option::where('name', $name)->first();

        return $option ?? new stdClass();
    }

    public function getValue(string $name)
    {
        return $this->get($name)->value;
    }

    public function update(string $name, string $value)
    {
        $validator = Validator::make([
            'name' => $name,
            'value' => $value
        ], [
            'name' => 'required|max:64|exists:translator_options,name',
            'value' => 'nullable|max:256'
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }

        $option = $this->get($name);

        if (!isset($option)){
            $this->errors = [
                'name' => 'Option not found.'
            ];
            return false;
        }

        $option->update(['name' => $name, 'value' => $value]);

        return true;
    }

    public function getValidationErrors()
    {
        return $this->errors;
    }
}