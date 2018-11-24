<?php namespace Shohabbos\Board\Models;

use Model;

/**
 * Model
 */
class Category extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sortable;
    use \October\Rain\Database\Traits\SimpleTree;
    
    const SORT_ORDER = 'sort';
    const PARENT_ID = 'parent_id';

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

    public $belongsTo = [
        'parent'    => [self::class, 'key' => 'parent_id'],
    ];
    
    public $hasMany = [
        'children'    => [self::class, 'key' => 'parent_id'],
        'properties' => Property::class,
        'posts' => Post::class
    ];

    public $attachOne = [
        'thumb' => 'System\Models\File'
    ];

    public function getParentIdOptions() {
        return self::where('parent_id', null)->lists('name', 'id');    
    }
}
