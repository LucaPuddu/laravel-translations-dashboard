<?php
/**
 * Created by PhpStorm.
 * User: Luca
 * Date: 3/6/2019
 * Time: 15:31
 */

namespace LPuddu\LaravelTranslationsDashboard\Repositories;

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
        $locale    = array_get($attributes, 'locale', '');
        $namespace = array_get($attributes, 'namespace', '');
        $group     = array_get($attributes, 'group', '');
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
            'group'     => ['required', 'alpha_dash'],
            'item'      => "alpha_dash|required|unique:{$table},item,NULL,id,locale,{$locale},namespace,{$namespace},group,{$group}",
            'text'      => '', // Translations may be empty
        ];
    }
}