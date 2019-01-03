<?php namespace Shohabbos\Board\Components;

use Auth;
use Flash;
use Input;
use Cookie;
use Redirect;
use Validator;
use Cms\Classes\Page;
use ValidationException;
use Cms\Classes\ComponentBase;
use Shohabbos\Board\Models\Post;
use Shohabbos\Board\Models\Plan;
use Shohabbos\Board\Models\Category;
use Shohabbos\Board\Models\Location;
use Shohabbos\Board\Models\PostProperty;

class BoardUser extends ComponentBase
{

    public $slug;
    public $user;
    public $posts;
    public $postPage;
    public $pageParam;
    public $postPerPage;
    public $postEditPage;
    public $noPostsMessage;
    public $redirectAfterForm;
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
            'slug' => [
                'title'       => 'Параметр URL',
                'description' => 'Параметр маршрута, необходимый для выбора конкретной записи.',
                'default'     => '{{ :slug }}',
                'type'        => 'string'
            ],
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
            'redirectAfterForm' => [
                'title'       => 'Перенаправление',
                'description' => 'Перенаправление после форма: добавления, редактирования, рекламировать',
                'type'        => 'dropdown',
                'default'     => 'profile',
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
        $this->slug = $this->page['slug'] = $this->property('slug');
        $this->postPage = $this->page['postPage'] = $this->property('postPage');
        $this->pageParam = $this->page['pageParam'] = $this->paramName('pageNumber');
        $this->postPerPage = $this->page['postPerPage'] = $this->property('postPerPage');
        $this->postEditPage = $this->page['postEditPage'] = $this->property('postEditPage');
        $this->noPostsMessage = $this->page['noPostsMessage'] = $this->property('noPostsMessage');
        $this->redirectAfterForm = $this->page['redirectAfterForm'] = $this->property('redirectAfterForm');
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

    public function getPlans() {
        return Plan::orderBy('id')->get();
    }

    public function loadPost() {
        $post = Post::where('user_id', $this->user->id)->where('slug', $this->slug)->first();

        if ($post) {
            $post->setUrl($this->postPage, $this->controller);
        }

        return $post;
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

    public function onAdvertising() {
        $post = $this->loadPost();

        if (!$post) {
            throw new ValidationException(['message' => 'Запись не найден']);
        }

        $data = Input::only(['plan']);

        $rules = [
            'plan' => 'required',
            'plan.*' => 'required|integer',
        ];

        $validation = Validator::make($data, $rules);

        if ($validation->fails()) {
            throw new ValidationException($validation);
        }


        try {
            // something
            $keys = array_keys($data['plan']);
            $plans = Plan::whereIn('id', $keys)->get();

            foreach ($plans as $plan) {
                $pivotData = ['expires_at' => $plan->getExpires()];
                $post->plans()->add($plan, $pivotData);
            }

        } catch (Exception $e) {
            throw new ValidationException($e);
        }
 
        return Redirect::to($this->redirectAfterForm);
    }

    public function onRemovePhoto() {
        $post = $this->loadPost();
        $photo = $post->images()->where('id', input('id'))->first();

        if (!$photo) {
            throw new ValidationException(['message' => 'Запись не найден']);
        }

        $photo->delete();
    }

    public function onEditPost() {
        $post = $this->loadPost();

        if (!$post) {
            throw new ValidationException(['message' => 'Запись не найден']);
        }

        unset($post->url);

        $data = Input::only([
            'title', 'content', 'category_id', 'location_id', 'amount', 'is_contract',
            'phone', 'email', 'contact_name', 'images', 'properties'
        ]);

        $rules = [
            'title' => 'required|min:10',
            'content' => 'required|min:20',
            'category_id' => 'required|exists:shohabbos_board_categories,id',
            'location_id' => 'required|exists:shohabbos_board_locations,id',
            'phone' => 'required|min:7',
            'email' => 'required|email',
            'amount' => 'required|integer',
            'is_contract' => 'boolean',
            'contact_name' => 'required|min:2',
            'images' => 'sometimes|required',
            'images.*' => 'image|mimes:jpg,jpeg,png,gif,bmp|dimensions:min_width=500,min_height=300',
            'properties' => 'sometimes|required',
        ];

        $validation = Validator::make($data, $rules);

        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        try {
            $post->properties()->delete();
            $data['status'] = 'new';
            $properties = [];
            
            if (!empty($data['properties'])) {
                foreach ($data['properties'] as $key => $value) {
                    if (empty($value)) {
                        continue;
                    }

                    $properties[] = new PostProperty([
                        'category_id' => $data['category_id'],
                        'property_id' => $key,
                        'value'       => $value
                    ]);
                }
            }

            $post->fill($data)->save();

            if (!empty($properties)) {
                $post->properties()->addMany($properties);
            }

        } catch (Exception $e) {
            throw new ValidationException($e);
        }

        return Redirect::to($this->redirectAfterForm);
    }

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
            'images.*' => 'image|mimes:jpg,jpeg,png,gif,bmp|dimensions:min_width=500,min_height=300',
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

        return Redirect::to($this->redirectAfterForm);
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
