<?php namespace Shohabbos\Board\Components;

use Cms\Classes\ComponentBase;
use Shohabbos\Board\Models\Post;

class Helper extends ComponentBase
{
    
    public $posts;

    public function componentDetails()
    {
        return [
            'name'        => 'shohabbos.board::lang.settings.helper_title',
            'description' => 'shohabbos.board::lang.settings.helper_description'
        ];
    }

    public function onRun() {
        $posts = array_keys(\Cookie::get('featured', []));

        if (!empty($posts)) {
            $this->posts = Post::whereIn('id', $posts)->get();
        }
    }

    public function onAddToList() {
        $class = "";
        $id = post('id');
        $posts = \Cookie::get('featured', []);

        if (isset($posts[$id])) {
            unset($posts[$id]);
            \Cookie::queue('featured', $posts);
            $class = "-empty";
        } elseif($post = Post::find($id)) {     
            $posts[$id] = [
                'title' => $post->title,
                'slug' => $post->slug,
                'poster' => $post->poster,
                'price' => $post->price
            ];

            \Cookie::queue('featured', $posts);
        }
    }

}
