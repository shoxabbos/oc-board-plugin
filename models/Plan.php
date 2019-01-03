<?php namespace Shohabbos\Board\Models;

use Model;
use Carbon\Carbon;

/**
 * Model
 */
class Plan extends Model
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
    public $table = 'shohabbos_board_plans';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $belongsToMany = [
        'posts' => [
            Post::class, 
            'table' => 'shohabbos_board_post_plan', 
            'timestamps' => true,
            'pivot' => ['created_at', 'updated_at', 'expires_at']
        ]
    ];

    public function getTypeOptions() {
        return [
            'up' => 'Up',
            'top' => 'Top',
            'vip' => 'Vip'
        ];
    }

    public function getExpires() {
        return Carbon::now()->addDay($this->days);
    }


}
