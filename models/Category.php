<?php namespace Shohabbos\Board\Models;

use Model;

/**
 * Model
 */
class Category extends Model
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
    public $table = 'shohabbos_board_categories';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $hasMany = [
        'properties' => 'Shohabbos\Board\Models\CategoryProperty'
    ];

    public function getParentIdOptions() {
        return self::where('parent_id', null)->lists('name', 'id');
    }

}
