<?php namespace Shohabbos\Board\Models;

use Model;

/**
 * Model
 */
class PropertyValue extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'shohabbos_board_property_values';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $belongsTo = [
        'property' => 'Shohabbos\Board\Models\Property'
    ];
}
