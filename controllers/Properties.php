<?php namespace Shohabbos\Board\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Properties extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\ReorderController',
        'Backend\Behaviors\RelationController'
    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';
    public $relationConfig = 'config_relation.yaml';

    public $requiredPermissions = [
        'manage_properties' 
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Shohabbos.Board', 'board-menu', 'board-properties');
    }
}
