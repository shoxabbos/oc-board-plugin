<?php namespace Shohabbos\Board\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

use Shohabbos\Board\Models\PostProperty;

class Posts extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        //'Backend\Behaviors\RelationController',
    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $relationConfig = 'config_relation.yaml';

    public $requiredPermissions = [
        'manage_posts' 
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Shohabbos.Board', 'board', 'board-posts');
    }

    public function formExtendModel($model)
    {
        /*
         * Init proxy field model if we are creating the model
         */
        if ($this->action == 'update') {
            $model->properties[] = new PostProperty;
            $model->properties[] = new PostProperty;
            $model->properties[] = new PostProperty;
            $model->properties[] = new PostProperty;
        }

        return $model;
    }

    public function formExtendFields($form)
    {
        $properties = $form->model->category->properties;

        foreach($properties as $key => $property) {
            $form->addTabFields([
                "properties[{$key}][value]" => [
                    'type' => $property->type,
                    'label'   => $property->label,
                    'default' => 'shox',
                    'comment' => $property->comment,
                    'span' => 'auto',
                    'options' => $property->values->lists('label', 'value')
                ],
            ]);
        
            $form->addTabFields([
                "properties[{$key}][key]" => [
                    'label' => 'Key',
                    'span' => 'auto',
                ],
            ]);

        }
    }

}
