<?php namespace Shohabbos\Board;

use \Event;
use Rainlab\User\Models\User;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{

    public $require = [
        'Rainlab.User',
    ];

    public function boot()
    {

        // Local event hook that affects all users
        User::extend(function($model) {
            $model->hasOne['location'] = 'Shohabbos\Board\Models\Location';
        });


        // Extend all backend list usage
        Event::listen('backend.list.extendColumns', function($widget) {

            // Only for the User controller
            if (!$widget->getController() instanceof \RainLab\User\Controllers\Users) {
                return;
            }

            // Only for the User model
            if (!$widget->model instanceof \RainLab\User\Models\User) {
                return;
            }

            $widget->addColumns([
                'balance' => [
                    'label' => 'Balance'
                ],
                'location_id' => [
                    'label' => 'Location'
                ]
            ]);

            $widget->addColumns([
                'id' => [
                    'label' => 'ID'
                ]
            ]);
        });
    }

    public function registerComponents()
    {
        return [
            'Shohabbos\Board\Components\Post' => 'boardPost',
            'Shohabbos\Board\Components\Posts' => 'boardPosts',
            'Shohabbos\Board\Components\PostForm' => 'boardPostForm',
            'Shohabbos\Board\Components\Categories' => 'boardCategories',
        ];
    }

    public function registerSettings()
    {
    }
}
