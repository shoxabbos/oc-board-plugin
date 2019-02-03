<?php namespace Shohabbos\Board\Components;

use Auth;
use Flash;
use Input;
use Validator;
use BackendAuth;
use Cms\Classes\Page;
use ValidationException;
use Cms\Classes\ComponentBase;
use Shohabbos\Board\Models\Post as BoardPost;
use Shohabbos\Board\Models\PostMessage;

class Post extends ComponentBase
{
    /**
     * @var Shohabbos\Board\Models\Post The post model used for display.
     */
    public $post;

    /**
     * @var string Reference to the page name for linking to category.
     */
    public $categoryPage;

    public function componentDetails()
    {
        return [
            'name'        => 'shohabbos.board::lang.settings.post_title',
            'description' => 'shohabbos.board::lang.settings.post_description'
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
                'default'     => 'list',
            ],
            'postPage' => [
                'title'       => 'shohabbos.board::lang.settings.posts_post',
                'description' => 'shohabbos.board::lang.settings.posts_post_description',
                'type'        => 'dropdown',
                'default'     => 'post/detail',
            ],
            'postsPerPage' => [
                'title'             => 'shohabbos.board::lang.settings.posts_per_page',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'shohabbos.board::lang.settings.posts_per_page_validation',
                'default'           => '4',
            ],
        ];
    }

    public function getCategoryPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getPostPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $this->postPage = $this->page['postPage'] = $this->property('postPage');
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
        $this->post = $this->page['post'] = $this->loadPost();

        if (!$this->post) {
            return \Redirect::to('404');
        }

        $this->page->title = $this->post->title;
    }

    public function onRender()
    {
        if (empty($this->post)) {
            $this->post = $this->page['post'] = $this->loadPost();
        }
    }

    public function onPostMessage() {
        $data = Input::only(['text', 'post_id', 'user_id']);

        $rules = [
            'text' => 'required|string',
            'user_id' => 'sometimes|exists:users,id',
            'post_id' => 'required|exists:shohabbos_board_posts,id',
        ];

        $validation = Validator::make($data, $rules);

        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        $message = new PostMessage($data);
        $message->save();
        
        Flash::success("Жалоба принята");
    }

    protected function loadPost()
    {
        $slug = $this->property('slug');

        $post = new BoardPost;

        $post = $post->isClassExtendedWith('RainLab.Translate.Behaviors.TranslatableModel')
            ? $post->transWhere('slug', $slug)
            : $post->where('slug', $slug);

        if (!$this->checkEditor()) {
            $post = $post->isPublished();
        }

        $post = $post->first();

        /*
         * Add a "url" helper attribute for linking to each category
         */
        if ($post && $post->category) {
            $post->category->setUrl($this->categoryPage, $this->controller);

            $post->increment('views');
        }

        return $post;
    }

    public function previousPost()
    {
        return $this->getPostSibling(-1);
    }

    public function nextPost()
    {
        return $this->getPostSibling(1);
    }

    public function similarPosts() {
        if (!$this->post) {
            return;
        }

        $posts = $this->post->category->posts()
            ->inRandomOrder()->where('id', '!=', $this->post->id)
            ->limit($this->property('postsPerPage'))
            ->get();

        /*
         * Add a "url" helper attribute for linking to each post and category
         */
        $posts->each(function($post) {
            $post->setUrl($this->postPage, $this->controller);
        });

        return $posts;
    }

    protected function getPostSibling($direction = 1)
    {
        if (!$this->post) {
            return;
        }

        $method = $direction === -1 ? 'previousPost' : 'nextPost';

        if (!$post = $this->post->$method()) {
            return;
        }

        $postPage = $this->getPage()->getBaseFileName();

        $post->setUrl($postPage, $this->controller);

        $post->category->setUrl($this->categoryPage, $this->controller);

        return $post;
    }

    protected function checkEditor()
    {
        $backendUser = BackendAuth::getUser();
        return $backendUser && $backendUser->hasAccess('shohabbos.board.access_posts');
    }
}
