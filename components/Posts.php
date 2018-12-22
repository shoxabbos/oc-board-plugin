<?php namespace Shohabbos\Board\Components;

use Redirect;
use BackendAuth;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Shohabbos\Board\Models\Post;
use Shohabbos\Board\Models\Category;
use Shohabbos\Board\Models\Location;

class Posts extends ComponentBase
{
    /**
     * A collection of posts to display
     * @var Collection
     */
    public $posts;

    /**
     * Parameter to use for the page number
     * @var string
     */
    public $pageParam;

    /**
     * Parameter to use for the search query
     * @var string
     */
    public $searchParam;

    /**
     * Parameter to use for the search query
     * @var string
     */
    public $locationParam;

    /**
     * If the post list should be filtered by a category, the model to use.
     * @var Model
     */
    public $category;

    /**
     * Message to display when there are no messages.
     * @var string
     */
    public $noPostsMessage;

    /**
     * Reference to the page name for linking to posts.
     * @var string
     */
    public $postPage;

    /**
     * Reference to the page name for linking to categories.
     * @var string
     */
    public $categoryPage;

    /**
     * If the post list should be ordered by another attribute.
     * @var string
     */
    public $sortOrder;

    public function componentDetails()
    {
        return [
            'name'        => 'shohabbos.board::lang.settings.posts_title',
            'description' => 'shohabbos.board::lang.settings.posts_description'
        ];
    }

    public function defineProperties()
    {
        return [
            'pageNumber' => [
                'title'       => 'shohabbos.board::lang.settings.posts_pagination',
                'description' => 'shohabbos.board::lang.settings.posts_pagination_description',
                'type'        => 'string',
                'default'     => '{{ :page }}',
            ],
            'categoryFilter' => [
                'title'       => 'shohabbos.board::lang.settings.posts_filter',
                'description' => 'shohabbos.board::lang.settings.posts_filter_description',
                'type'        => 'string',
                'default'     => ''
            ],
            'searchParam' => [
                'title'       => 'shohabbos.board::lang.settings.search_param_title',
                'description' => 'shohabbos.board::lang.settings.search_param_description',
                'type'        => 'string',
                'default'     => '{{ :query }}'
            ],
            'locationParam' => [
                'title'       => 'shohabbos.board::lang.settings.location_param_title',
                'description' => 'shohabbos.board::lang.settings.location_param_description',
                'type'        => 'string',
                'default'     => '{{ :location }}'
            ],
            'postsPerPage' => [
                'title'             => 'shohabbos.board::lang.settings.posts_per_page',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'shohabbos.board::lang.settings.posts_per_page_validation',
                'default'           => '10',
            ],
            'noPostsMessage' => [
                'title'        => 'shohabbos.board::lang.settings.posts_no_posts',
                'description'  => 'shohabbos.board::lang.settings.posts_no_posts_description',
                'type'         => 'string',
                'default'      => 'No posts found',
                'showExternalParam' => false
            ],
            'sortOrder' => [
                'title'       => 'shohabbos.board::lang.settings.posts_order',
                'description' => 'shohabbos.board::lang.settings.posts_order_description',
                'type'        => 'dropdown',
                'default'     => 'created_at desc'
            ],
            'categoryPage' => [
                'title'       => 'shohabbos.board::lang.settings.posts_category',
                'description' => 'shohabbos.board::lang.settings.posts_category_description',
                'type'        => 'dropdown',
                'default'     => 'blog/category',
                'group'       => 'Links',
            ],
            'postPage' => [
                'title'       => 'shohabbos.board::lang.settings.posts_post',
                'description' => 'shohabbos.board::lang.settings.posts_post_description',
                'type'        => 'dropdown',
                'default'     => 'blog/post',
                'group'       => 'Links',
            ],
            'exceptPost' => [
                'title'             => 'shohabbos.board::lang.settings.posts_except_post',
                'description'       => 'shohabbos.board::lang.settings.posts_except_post_description',
                'type'              => 'string',
                'validationPattern' => 'string',
                'validationMessage' => 'shohabbos.board::lang.settings.posts_except_post_validation',
                'default'           => '',
                'group'             => 'Exceptions',
            ],
            'exceptCategories' => [
                'title'             => 'shohabbos.board::lang.settings.posts_except_categories',
                'description'       => 'shohabbos.board::lang.settings.posts_except_categories_description',
                'type'              => 'string',
                'default'           => '',
                'group'             => 'Exceptions',
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

    public function getSortOrderOptions()
    {
        return Post::$allowedSortingOptions;
    }

    public function onRun()
    {
        $this->prepareVars();

        $this->category = $this->page['category'] = $this->loadCategory();
        $this->posts = $this->page['posts'] = $this->listPosts();

        /*
         * If the page number is not valid, redirect
         */
        if ($pageNumberParam = $this->paramName('pageNumber')) {
            $currentPage = $this->property('pageNumber');

            if ($currentPage > ($lastPage = $this->posts->lastPage()) && $currentPage > 1)
                return Redirect::to($this->currentPageUrl([$pageNumberParam => $lastPage]));
        }
    }

    protected function prepareVars()
    {
        $this->pageParam = $this->page['pageParam'] = $this->paramName('pageNumber');
        $this->searchParam = $this->page['searchParam'] = $this->paramName('searchParam');
        $this->locationParam = $this->page['locationParam'] = $this->paramName('locationParam');

        $this->noPostsMessage = $this->page['noPostsMessage'] = $this->property('noPostsMessage');

        /*
         * Page links
         */
        $this->postPage = $this->page['postPage'] = $this->property('postPage');
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
    }

    protected function listPosts()
    {
        $category = $this->category ? $this->category->id : null;

        /*
         * List all the posts, eager load their categories
         */
        $isPublished = !$this->checkEditor();

        $posts = Post::listFrontEnd([
            'page'             => $this->property('pageNumber'),
            'sort'             => $this->property('sortOrder'),
            'perPage'          => $this->property('postsPerPage'),
            'search'           => trim(input($this->searchParam)),
            'location'         => trim(input($this->locationParam)),
            'category'         => $category,
            'published'        => $isPublished
        ]);

        /*
         * Add a "url" helper attribute for linking to each post and category
         */
        $posts->each(function($post) {
            $post->setUrl($this->postPage, $this->controller);

            $post->category->setUrl($this->categoryPage, $this->controller);
        });

        return $posts;
    }

    protected function loadCategory()
    {
        if (!$slug = $this->property('categoryFilter')) {
            return null;
        }

        $slugs = explode("/", $slug);
        $slug = end($slugs);

        $category = new Category;

        $category = $category->isClassExtendedWith('RainLab.Translate.Behaviors.TranslatableModel')
            ? $category->transWhere('slug', $slug)
            : $category->where('slug', $slug);

        $category = $category->first();

        return $category ?: null;
    }

    protected function checkEditor()
    {
        $backendUser = BackendAuth::getUser();
        return $backendUser && $backendUser->hasAccess('shohabbos.board.access_posts');
    }






    // variables for frontend

    public function locations() {
        return Location::getNested();
    }

    public function categories() {
        return Category::getNested();
    }

}
