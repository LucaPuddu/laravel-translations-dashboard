<?php
/**
 * Created by PhpStorm.
 * User: Luca
 * Date: 05/03/2019
 * Time: 00:53
 */

namespace LPuddu\LaravelTranslationsDashboard\Repositories;


use Illuminate\Support\Arr;
use Waavi\Translation\Models\Language;

class LanguageRepository extends \Waavi\Translation\Repositories\LanguageRepository
{
    /**
     *  Validate the given attributes
     *
     *  @param  array    $attributes
     *  @return boolean
     */
    public function validate(array $attributes)
    {
        $id    = Arr::get($attributes, 'id', 'NULL');
        $table = $this->model->getTable();
        $rules = [
            'locale' => "required|unique:{$table},locale,{$id}",
            'name'   => "required|unique:{$table},name,{$id}",
            'visible' => "boolean|nullable"
        ];
        $validator = $this->validator->make($attributes, $rules);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }

    /**
     * Create model or restores it if it existed already
     *
     * @param array $attributes
     * @return null|Language
     */
    public function createOrRestore(array $attributes){
        $rules = [
            'locale' => "required",
            'name'   => "required",
            'visible' => "boolean|nullable"
        ];
        $validator = $this->validator->make($attributes, $rules);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return null;
        }

        $lang = $this->findTrashedByLocale($attributes['locale']);
        if (isset($lang)) {
            $lang = $this->restore($lang->id);
        } else {
            $lang = $this->create(['name' => $attributes['name'], 'locale' => $attributes['locale']]);
        }
        return $lang;
    }
}
