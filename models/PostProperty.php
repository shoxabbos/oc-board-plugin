<?php namespace Shohabbos\Board\Models;

use Model;

/**
 * Model
 */
class PostProperty extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'shohabbos_board_post_properties';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
