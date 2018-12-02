<?php namespace Shohabbos\Board\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

use Shohabbos\Board\Models\PostProperty;

class Posts extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\RelationController',
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

    
    public function formAfterSave($model)
    {
        if ($model->attrs) {
            
            $model->properties()->delete();

            $properties = [];
            foreach($model->attrs as $key => $value) {
                $model->properties()->create([
                    'property_id' => $key,
                    'category_id' => $model->category_id,
                    'value' => is_array($value) ? json_encode($value) : $value,
                ]);
            }

        }
    } 



    public function formExtendFields($form)
    {
    
        if ($form->model->category) {
            $properties = $form->model->category->properties;

            foreach($properties as $key => $property) {                
                $form->addTabFields([
                    "attrs[{$property->id}]" => [
                        'type' => $property->type,
                        'label'   => $property->label,
                        'comment' => $property->comment,
                        'span' => 'auto',
                        'options' => $property->values->lists('label', 'value'),
                    ],
                ]);
            }
        }

        
    }

}
