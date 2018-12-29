<?php namespace Shohabbos\Board\Components;

use Flash;
use Input;
use Cookie;
use Validator;
use Cms\Classes\Page;
use ValidationException;
use Cms\Classes\ComponentBase;
use Shohabbos\Board\Models\Post;

class Wishlist extends ComponentBase
{

    public $posts;
    public $postPage;
    public $showMessage;
    public $noPostsMessage;

    public function componentDetails()
    {
        return [
            'name'        => 'shohabbos.board::lang.settings.withlist_title',
            'description' => 'shohabbos.board::lang.settings.withlist_description'
        ];
    }

    public function defineProperties()
    {
        return [
            'postPage' => [
                'title'       => 'shohabbos.board::lang.settings.posts_post',
                'description' => 'shohabbos.board::lang.settings.posts_post_description',
                'type'        => 'dropdown',
                'default'     => 'post/detail',
                'group'       => 'Links',
            ],
            'showMessage' => [
                'showExternalParam' => false,
                'title'       => 'Отображать сообщение',
                'description' => 'Показать уведомления после нажатия на кнопку "Добавить в избранные"',
                'type'        => 'dropdown',
                'default'     => '1',
                'options'     => [
                    '0' => 'No',
                    '1' => 'Yes',
                ]
            ],
            'noPostsMessage' => [
                'title'        => 'shohabbos.board::lang.settings.posts_no_posts',
                'description'  => 'shohabbos.board::lang.settings.posts_no_posts_description',
                'type'         => 'string',
                'default'      => 'No posts found',
                'showExternalParam' => false
            ]
        ];
    }

    protected function prepareVars() {
        $this->postPage = $this->page['postPage'] = $this->property('postPage');
        $this->showMessage = $this->page['showMessage'] = $this->property('showMessage');
        $this->noPostsMessage = $this->page['noPostsMessage'] = $this->property('noPostsMessage');
    }

    public function init() {
        $this->prepareVars();
    }

    

    public function loadPosts() {
        $posts = array_keys(Cookie::get('wishlist', []));

        if (!empty($posts)) {
            $posts = Post::whereIn('id', $posts)->get();

            /*
             * Add a "url" helper attribute for linking to each post and category
             */
            $posts->each(function($post) {
                $post->setUrl($this->postPage, $this->controller);
            });
        }

        return $posts;
    }

    public function onAddToList() {
        $data = Input::only(['post_id']);
        $rules = [
            'post_id' => 'required|exists:shohabbos_board_posts,id',
        ];

        $validation = Validator::make($data, $rules);

        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        $class = "";
        $id = $data['post_id'];
        $posts = Cookie::get('wishlist', []);

        if (isset($posts[$id])) {
            unset($posts[$id]);
            Cookie::queue('wishlist', $posts);
            $class = "-empty";
        } else {
            $posts[$id] = $id;
            Cookie::queue('wishlist', $posts);

            if ($this->showMessage) {
                Flash::success(_('Добавлено в избранные!'));
            }
        }
    }



    public function getPostPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

}
