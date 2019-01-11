<?php namespace Shohabbos\Board\Components;

use Auth;
use Mail;
use Flash;
use Input;
use Cookie;
use Redirect;
use Validator;
use Carbon\Carbon;
use Cms\Classes\Page;
use ValidationException;
use Cms\Classes\ComponentBase;
use Shohabbos\Board\Models\Post;
use Shohabbos\Board\Models\Plan;
use Shohabbos\Board\Models\Category;
use Shohabbos\Board\Models\Location;
use Shohabbos\Board\Models\PostProperty;
use Shohabbos\Board\Models\BalanceHistory;

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
            'published'        => null,
            'user_id'          => $this->user->id,
            'perPage'          => $this->postPerPage,
            'page'             => $this->property('pageNumber'),
            'sort'             => 'id desc'
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
    
    public function loadHistory($page = 1, $count = 15) {
        return $this->user->history()->orderByDesc('id')->paginate($count, $page);
    }


    // ---------------------------- handlers ----------------------------

    public function onDeactivatePost() {
        $post = Post::find(input('id'));
        if (!$post) {
            throw new ValidationException(['message' => 'Запись не найден']);
        }

        $post->published = false;
        $post->save();

        return Redirect::to($this->redirectAfterForm);
    }

    public function onPublishPost() {
        $post = Post::find(input('id'));
        if (!$post) {
            throw new ValidationException(['message' => 'Запись не найден']);
        }
        
        if (empty($post->published_at)) {
            Flash::info('Объявление еще не прошла модерацию.');
        } else {
            $post->published = true;
            $post->published_at = Carbon::now();
            $post->save();

            return Redirect::to($this->redirectAfterForm);
        }
    }

    public function onLoadHistory() {
        $this->page['history'] = $this->loadHistory(input('page'));
    }
 
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

        // something
        $keys = array_keys($data['plan']);
        $sum = Plan::whereIn('id', $keys)->sum('amount');

        if ($this->user->balance < $sum) {
            throw new ValidationException(['message' => 'На вашем счету не достаточно средств']);
        }

        try {
            $plans = Plan::whereIn('id', $keys)->get();
            

            foreach ($plans as $plan) {
                $pivotData = ['expires_at' => $plan->getExpires()];
                $post->plans()->add($plan, $pivotData);

                $amount = number_format($plan->amount, 0, ' ', ' ');
                $expires = $plan->getExpires()->format('H:i d/m/Y');

                $this->user->balance -= $plan->amount;
                if ($this->user->save()) {
                    $this->user->history()->add(new BalanceHistory([
                        'amount' => "-{$amount} сум",
                        'message' => "Платная услуга \"{$plan->name}\" до {$expires}"
                    ]));
                }

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
            'phone', 'email', 'contact_name', 'images', 'properties', 'currency'
        ]);

        $rules = [
            'currency' => 'required|in:uzs,usd',
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
            $properties = [];
            $data['attrs'] = [];
            
            if (!empty($data['properties'])) {
                foreach ($data['properties'] as $key => $value) {
                    if (empty($value)) {
                        continue;
                    }

                    $data['attrs'][$key] = $value;

                    $properties[] = new PostProperty([
                        'category_id' => $data['category_id'],
                        'property_id' => $key,
                        'value'       => $value
                    ]);
                }
            }

            // save properties as json
            unset($data['properties']);

            $data['attrs'] = [];
            

            $post->published = false;
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
            'phone', 'email', 'contact_name', 'images', 'properties', 'currency'
        ]);

        $data['user_id'] = $user ? $user->id : null;

        $rules = [
            'currency' => 'required|in:uzs,usd',
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

            $model = Post::create($data);
            $model->published_at = Carbon::now();
            $model->published = true;
            $model->save();

            if (!empty($properties)) {
                $model->properties()->addMany($properties);
            }

            $model->setUrl($this->postPage, $this->controller);

            $this->sendAdminNotify("shohabbos.board::mail.new-post", [
                'user' => $this->user->name,
                'post' => $model->url
            ]);

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



    // private functions
    public function sendAdminNotify($view, $data) {
        Mail::send($view, $data, function($msg){
            $admins = \Backend\Models\User::all();
            foreach($admins as $admin) {
                $msg->to($admin->email, $admin->name);
            }
        });
    }

}
