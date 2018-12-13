<?php namespace Shohabbos\Board\Components;

use Auth;
use Input;
use Validator;
use BackendAuth;
use Cms\Classes\Page;
use ValidationException;
use Cms\Classes\ComponentBase;
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
        $this->page['categories'] = BoardCategory::with('children')->where('parent_id', null)->get();
        $this->page['locations'] = BoardLocation::with('children')->where('parent_id', null)->get();
    }

    public function onCreatePost() {
        $user = Auth::getUser();
        $data = Input::only(['title', 'content', 'category_id', 'location_id', 'phone', 'email', 'contact_name', 'images']);
        $data['user_id'] = $user ? $user->id : null;

        $rules = [
            'user_id' => 'required|exists:users,id',
            'title' => 'required|min:10',
            'content' => 'required|min:20',
            'category_id' => 'required|exists:shohabbos_board_categories,id',
            'location_id' => 'required|exists:shohabbos_board_locations,id',
            'phone' => 'required|min:7',
            'email' => 'required|email',
            'contact_name' => 'required|min:2',
            'images' => 'required',
            'images.*' => 'image|mimes:jpg,jpeg'
        ];

        $validation = Validator::make($data, $rules);

        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        try {
            $data['status'] = 'new'; 
            $model = BoardPost::create($data);
        } catch (Exception $e) {
            throw new ValidationException($e);
        }

    }



}
