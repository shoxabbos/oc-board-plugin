<?php namespace Shohabbos\Board\Models;

use Model;

/**
 * Model
 */
class BalanceHistory extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'shohabbos_board_balance_history';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $belongsTo = [
        'user' => 'RainLab\User\Models\User',
    ];

}
