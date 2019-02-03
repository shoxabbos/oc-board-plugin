<?php namespace Shohabbos\Board\Models;

use Model;

/**
 * Model
 */
class Review extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'shohabbos_board_reviews';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $guarded = ['id'];

    public $belongsTo = [
        'user' => 'RainLab\User\Models\User',
        'author' => 'RainLab\User\Models\User',
    ];
}
