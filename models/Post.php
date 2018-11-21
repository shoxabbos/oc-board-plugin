<?php namespace Shohabbos\Board\Models;

use Model;

/**
 * Model
 */
class Post extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'shohabbos_board_posts';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $attachMany = [
        'images' => 'System\Models\File'
    ];

    public function getCategoryIdOptions() {
        return Category::lists('name', 'id');
    }

    public $belongsTo = [
        'user' => 'RainLab\User\Models\User'
    ];
}
