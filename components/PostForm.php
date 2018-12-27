<?php namespace Shohabbos\Board\Components;

use Auth;
use Input;
use Validator;
use BackendAuth;
use Cms\Classes\Page;
use ValidationException;
use Cms\Classes\ComponentBase;
use Shohabbos\Board\Models\PostProperty;
use Shohabbos\Board\Models\Post as BoardPost;
use Shohabbos\Board\Models\Category as BoardCategory;
use Shohabbos\Board\Models\Location as BoardLocation;

class PostForm extends ComponentBase
{
    /**
     * @var Shohabos\Board\Models\Post The post model used for display.
     */
    public $post;

    /**
     * @var string Reference to the page name for linking to categories.
     */
    public $categoryPage;

    public function componentDetails()
    {
        return [
            'name'        => 'shohabbos.board::lang.settings.postform_title',
            'description' => 'shohabbos.board::lang.settings.postform_description'
        ];
    }

    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       => 'shohabbos.board::lang.settings.post_slug',
                'description' => 'shohabbos.board::lang.settings.post_slug_description',
                'default'     => '{{ :slug }}',
                'type'        => 'string'
            ],
            'categoryPage' => [
                'title'       => 'shohabbos.board::lang.settings.post_category',
                'description' => 'shohabbos.board::lang.settings.post_category_description',
                'type'        => 'dropdown',
                'default'     => 'blog/category',
            ],
        ];
    }

    public function onRun() {
        $this->page['categories'] = BoardCategory::getNested();
        $this->page['locations'] = BoardLocation::getNested();
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
            'images.*' => 'image|mimes:jpg,jpeg,png,gif,bmp',
            'properties' => 'sometimes|required',
        ];

        $validation = Validator::make($data, $rules);

        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        try {
            $properties = [];

            foreach ($data['properties'] as $key => $value) {
                $properties[] = new PostProperty([
                    'category_id' => $data['category_id'],
                    'property_id' => $key,
                    'value'       => $value
                ]);
            }

            $data['status'] = 'new'; 
            $model = BoardPost::create($data);

            $model->properties()->addMany($properties);

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

        $category = BoardCategory::find($data['category_id']);

        $this->page['properties'] = $category->properties;
    }

}
