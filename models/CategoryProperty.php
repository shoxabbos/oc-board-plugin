<?php namespace Shohabbos\Board\Models;

use Model;

/**
 * Model
 */
class CategoryProperty extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'shohabbos_board_category_properties';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public function getPropertyIdOptions() {
        return Property::all()->lists('name', 'id');
    }
}
