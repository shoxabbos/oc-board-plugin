<?php namespace Shohabbos\Board\Models;

use Model;

/**
 * Model
 */
class Location extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\NestedTree;
    use \October\Rain\Database\Traits\Sluggable;

    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];
    
    public $translatable = [
        'name', 'slug'
    ];

    protected $slugs = ['slug' => 'name'];

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
        'posts' => Post::class,
    ];
    
}
