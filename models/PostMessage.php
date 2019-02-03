<?php namespace Shohabbos\Board\Models;

use Model;

/**
 * Model
 */
class PostMessage extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'shohabbos_board_post_messages';

    public $guarded = ['id'];

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $belongsTo = [
        'user' => 'RainLab\User\Models\User',
        'post' => 'Shohabbos\Board\Models\Post',
    ];

}
