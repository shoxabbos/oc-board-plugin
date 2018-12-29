<?php namespace Shohabbos\Board\Components;

use Auth;
use Flash;
use Input;
use Cookie;
use Validator;
use Cms\Classes\Page;
use ValidationException;
use Cms\Classes\ComponentBase;
use Shohabbos\Board\Models\Post;

class UserPost extends ComponentBase
{

    public $user;
    public $posts;
    public $postPage;
    public $pageParam;
    public $postPerPage;
    public $noPostsMessage;

    public function componentDetails()
    {
        return [
            'name'        => 'Объявления пользователя',
            'description' => 'Отображает объявления пользователя на страницу'
        ];
    }

    public function defineProperties()
    {
        return [
            'pageNumber' => [
                'title'       => 'Параметр постраничной навигации',
                'description' => 'Параметр, необходимый для постраничной навигации.',
                'type'        => 'string',
                'default'     => '{{ :page }}',
            ],
            'postPage' => [
                'title'       => 'Страница просмотра',
                'description' => 'Название страницы для ссылки "подробнее". Это свойство используется по умолчанию компонентом.',
                'type'        => 'dropdown',
                'default'     => 'detail',
                'group'       => 'Links',
            ],
            'postPerPage' => [
                'title'             => 'Объявлений на странице',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'Недопустимый Формат. Ожидаемый тип данных - действительное число.',
                'default'           => '5',
            ],
            'noPostsMessage' => [
                'title'        => 'Отсутсвие записей',
                'description'  => 'Сообщение, отображаемое на странице, если нет никаких записей. Это свойство используется по умолчанию компонентом.',
                'type'         => 'string',
                'default'      => 'Сейчас у вас нет объявлений',
                'showExternalParam' => false
            ]
        ];
    }

    public function getPostPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    protected function prepareVars() {
        $this->user = Auth::getUser();
        $this->postPage = $this->page['postPage'] = $this->property('postPage');
        $this->pageParam = $this->page['pageParam'] = $this->paramName('pageNumber');
        $this->postPerPage = $this->page['postPerPage'] = $this->property('postPerPage');
        $this->noPostsMessage = $this->page['noPostsMessage'] = $this->property('noPostsMessage');
    }

    public function init() {
        $this->prepareVars();
    }

    

    // ---------------------------- client code ----------------------------

    public function loadPosts() {
        $posts = Post::with(['images', 'category', 'location'])->listFrontEnd([
            'user_id'          => $this->user->id,
            'perPage'          => $this->postPerPage,
            'page'             => $this->property('pageNumber')
        ]);

        $posts->each(function($post) {
            $post->setUrl($this->postPage, $this->controller);
        });

        return $posts;
    }
    

}
