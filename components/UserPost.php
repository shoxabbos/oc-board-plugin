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
use Shohabbos\Board\Models\Category;
use Shohabbos\Board\Models\Location;
use Shohabbos\Board\Models\PostProperty;

class UserPost extends ComponentBase
{

    public $user;
    public $posts;
    public $postPage;
    public $pageParam;
    public $postPerPage;
    public $postEditPage;
    public $noPostsMessage;
    public $postAdvertisingPage;

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
                'default'     => 'post/detail',
                'group'       => 'Links',
            ],
            'postEditPage' => [
                'title'       => 'Страница редактирования',
                'description' => 'Название страницы для ссылки "редактировать". Это свойство используется по умолчанию компонентом.',
                'type'        => 'dropdown',
                'default'     => 'post/edit',
                'group'       => 'Links',
            ],
            'postAdvertisingPage' => [
                'title'       => 'Страница рекламировать',
                'description' => 'Название страницы для ссылки "рекламировать". Это свойство используется по умолчанию компонентом.',
                'type'        => 'dropdown',
                'default'     => 'post/advertising',
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

    public function getPostEditPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getPostAdvertisingPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    protected function prepareVars() {
        $this->user = Auth::getUser();
        $this->postPage = $this->page['postPage'] = $this->property('postPage');
        $this->pageParam = $this->page['pageParam'] = $this->paramName('pageNumber');
        $this->postPerPage = $this->page['postPerPage'] = $this->property('postPerPage');
        $this->postEditPage = $this->page['postEditPage'] = $this->property('postEditPage');
        $this->noPostsMessage = $this->page['noPostsMessage'] = $this->property('noPostsMessage');
        $this->postAdvertisingPage = $this->page['postAdvertisingPage'] = $this->property('postAdvertisingPage');
    }

    public function init() {
        $this->prepareVars();
    }

    



    // ---------------------------- client code ----------------------------
    public function getLocations() {
        return Location::getNested();
    }

    public function getCategories() {
        return Category::getNested();
    }

    public function loadPosts() {
        $posts = Post::with(['images', 'category', 'location'])->listFrontEnd([
            'user_id'          => $this->user->id,
            'perPage'          => $this->postPerPage,
            'page'             => $this->property('pageNumber')
        ]);

        $posts->each(function($post) {
            $post->setUrl($this->postPage, $this->controller);

            $post->editPage = $this->controller->pageUrl($this->postEditPage, [
                'slug' => $post->slug
            ]);

            $post->advertisingPage = $this->controller->pageUrl($this->postAdvertisingPage, [
                'slug' => $post->slug
            ]);
        });

        return $posts;
    }
    



    // ---------------------------- handlers ----------------------------

    public function onCreatePost() {
        $user = Auth::getUser();
        $data = Input::only([
            'title', 'content', 'category_id', 'location_id', 'amount', 'is_contract',
            'phone', 'email', 'contact_name', 'images', 'properties'
        ]);

        $data['user_id'] = $user ? $user->id : null;

        $rules = [
            'user_id' => 'required|exists:users,id',
            'title' => 'required|min:10',
            'content' => 'required|min:20',
            'category_id' => 'required|exists:shohabbos_board_categories,id',
            'location_id' => 'required|exists:shohabbos_board_locations,id',
            'phone' => 'required|min:7',
            'email' => 'required|email',
            'amount' => 'required|integer',
            'is_contract' => 'boolean',
            'contact_name' => 'required|min:2',
            'images' => 'required',
            'images.*' => 'image|mimes:jpg,jpeg,png,gif,bmp',
            'properties' => 'sometimes|required',
        ];

        $validation = Validator::make($data, $rules);

        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        try {
            $properties = [];

            if (!empty($data['properties'])) {
                foreach ($data['properties'] as $key => $value) {
                    $properties[] = new PostProperty([
                        'category_id' => $data['category_id'],
                        'property_id' => $key,
                        'value'       => $value
                    ]);
                }    
            }

            $data['status'] = 'new'; 
            $model = Post::create($data);

            if (!empty($properties)) {
                $model->properties()->addMany($properties);
            }

        } catch (Exception $e) {
            throw new ValidationException($e);
        }
    }


    public function onLoadProperties() {
        $user = Auth::getUser();
        $data = Input::only(['category_id']);
        $data['user_id'] = $user ? $user->id : null;

        $rules = [
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:shohabbos_board_categories,id',
        ];

        $validation = Validator::make($data, $rules);

        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        $category = Category::find($data['category_id']);

        $this->page['properties'] = $category->properties;
    }



}
