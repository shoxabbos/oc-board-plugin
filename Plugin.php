<?php namespace Shohabbos\Board;

use \Event;
use Rainlab\User\Models\User;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{

    public $require = [
        'Rainlab.User',
        'Shohabbos.Payme',
    ];

    public function registerMailTemplates()
    {
        return [
            'shohabbos.board::mail.new-post',
        ];
    }

    public function boot()
    {
        // Local event hook that affects all users
        User::extend(function($model) {
            $model->addFillable([
                'location_id',
                'phone'
            ]);

            $model->belongsTo['location'] = 'Shohabbos\Board\Models\Location';
            $model->hasMany['posts'] = 'Shohabbos\Board\Models\Post';
            $model->hasMany['history'] = 'Shohabbos\Board\Models\BalanceHistory';
            $model->hasMany['reviews'] = ['Shohabbos\Board\Models\Review'];

            $model->addDynamicMethod('getRating', function($user) use ($model) {

                $query = $user->reviews()->get();
                $count = $query->count();
                $sum = $query->sum('stars');

                if ($sum) {
                    return round($sum / $count);
                } else {
                    return 0;
                }

            });
        });


        // now your actual code for extending fields
        \RainLab\User\Controllers\Users::extendFormFields(function($form, $model, $context){
            
            if (!$model instanceof \RainLab\User\Models\User)
                return;

            if (!$model->exists)
                return;

            $form->addTabFields([
                'phone' => [
                    'tab' => 'Board fields',
                    'type'  => 'text',
                    'label' => 'Phone',
                    'comment' => 'Add user phone'
                ],
                'balance' => [
                    'tab' => 'Board fields',
                    'type'  => 'text',
                    'label' => 'Balance',
                    'comment' => 'Add user phone'
                ],
                'location' => [
                    'tab' => 'Board fields',
                    'type'  => 'relation',
                    'label' => 'Location',
                    'comment' => 'Add user phone'
                ]
            ]);
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
                'phone' => [
                    'label' => 'Phone'
                ],
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
            'Shohabbos\Board\Components\Helper' => 'boardHelper',
            'Shohabbos\Board\Components\Review' => 'boardReview',
            'Shohabbos\Board\Components\Wishlist' => 'boardWishlist',
            'Shohabbos\Board\Components\BoardUser' => 'boardUserPost',
            'Shohabbos\Board\Components\Categories' => 'boardCategories',
        ];
    }

    public function registerSettings()
    {
    }
}
