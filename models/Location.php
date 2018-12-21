<?php namespace Shohabbos\Board\Models;

use Model;

/**
 * Model
 */
class Location extends Model
{
    use \October\Rain\Database\Traits\Sortable;
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SimpleTree;

    const SORT_ORDER = 'sort';
    const PARENT_ID = 'parent_id';

    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];
    
    public $translatable = [
        'name', 'slug'
    ];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'shohabbos_board_locations';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $hasMany = [
        'children'    => [self::class, 'key' => 'parent_id'],
        'posts' => Post::class,
    ];

    public $belongsTo = [
        'parent'    => [self::class, 'key' => 'parent_id'],
    ];

    public function getParentIdOptions() {
        return self::where('parent_id', null)
            ->where('id', '!=', $this->id)
            ->lists('name', 'id');
    }
    
}
