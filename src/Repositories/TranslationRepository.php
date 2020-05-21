<?php
/**
 * Created by PhpStorm.
 * User: Luca
 * Date: 3/6/2019
 * Time: 15:31
 */

namespace LPuddu\LaravelTranslationsDashboard\Repositories;

use Illuminate\Support\Arr;
use LPuddu\LaravelTranslationsDashboard\Rules\KebabCase;
use LPuddu\LaravelTranslationsDashboard\Rules\KebabCaseAndDot;

class TranslationRepository extends \Waavi\Translation\Repositories\TranslationRepository
{
    /**
     *  Validate the given attributes
     *
     *  @param  array    $attributes
     *  @return boolean
     */
    public function validate(array $attributes)
    {
        $table     = $this->model->getTable();
        $locale    = Arr::get($attributes, 'locale', '');
        $namespace = Arr::get($attributes, 'namespace', '');
        $group     = Arr::get($attributes, 'group', '');
        $rules     = $this->getValidationRules($table, $locale, $namespace, $group);
        $validator = $this->app['validator']->make($attributes, $rules);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }

    public function getValidationRules($table, $locale, $namespace, $group){
        return [
            'locale'    => 'required',
            'namespace' => 'required',
            'group'     => ['required', new KebabCase()],
            'item'      => [new KebabCaseAndDot(), "required","unique:{$table},item,NULL,id,locale,{$locale},namespace,{$namespace},group,{$group}"],
            'text'      => '', // Translations may be empty
        ];
    }
}
