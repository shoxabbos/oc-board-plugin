<?php namespace Shohabbos\Board\Models;

use Model;

/**
 * Model
 */
class Property extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'shohabbos_board_properties';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $jsonable = [
        'settings'
    ];

    public $hasMany = [
        'values' => 'Shohabbos\Board\Models\PropertyValue'
    ];
    
}
