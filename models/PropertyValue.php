<?php namespace Shohabbos\Board\Models;

use Model;

/**
 * Model
 */
class PropertyValue extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];

    public $translatable = [
        'label'
    ];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'shohabbos_board_property_values';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
