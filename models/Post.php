<?php namespace Shohabbos\Board\Models;

use Model;

/**
 * Model
 */
class Post extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'shohabbos_board_posts';

    public $jsonable = ['attrs'];

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $attachMany = [
        'images' => 'System\Models\File'
    ];

    public $belongsTo = [
        'user' => 'RainLab\User\Models\User',
        'category' => Category::class
    ];
    
    public $belongsToMany = [
        'plans' => [Plan::class, 'table' => 'shohabbos_board_post_plan']
    ];
    
    public $hasMany = [
        'properties' => PostProperty::class,
    ];

    public function getStatusOptions() {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'pending' => 'Pending',
        ];
    }

}
