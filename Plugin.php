<?php namespace Shohabbos\Board;

use System\Classes\PluginBase;
use \Event;

class Plugin extends PluginBase
{

    public function boot()
    {
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
            'Shohabbos\Board\Components\Categories' => 'boardCategories',
        ];
    }

    public function registerSettings()
    {
    }
}
