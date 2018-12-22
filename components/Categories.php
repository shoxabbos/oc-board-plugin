<?php namespace Shohabbos\Board\Components;

use Db;
use App;
use Request;
use Carbon\Carbon;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Shohabbos\Board\Models\Category as Category;

class Categories extends ComponentBase
{
    /**
     * @var Collection A collection of categories to display 
     */
    public $categories;

    /**
     * @var string Reference to the page name for linking to categories.
     */
    public $categoryPage;

    /**
     * @var string Reference to the current category slug.
     */
    public $currentCategorySlug;

    public function componentDetails()
    {
        return [
            'name'        => 'shohabbos.board::lang.settings.category_title',
            'description' => 'shohabbos.board::lang.settings.category_description'
        ];
    }

    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       => 'shohabbos.board::lang.settings.category_slug',
                'description' => 'shohabbos.board::lang.settings.category_slug_description',
                'default'     => '{{ :slug }}',
                'type'        => 'string'
            ],
            'displayEmpty' => [
                'title'       => 'shohabbos.board::lang.settings.category_display_empty',
                'description' => 'shohabbos.board::lang.settings.category_display_empty_description',
                'type'        => 'checkbox',
                'default'     => 0
            ],
            'categoryPage' => [
                'title'       => 'shohabbos.board::lang.settings.category_page',
                'description' => 'shohabbos.board::lang.settings.category_page_description',
                'type'        => 'dropdown',
                'default'     => 'board/category',
                'group'       => 'Links',
            ],
        ];
    }

    public function getCategoryPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $this->currentCategorySlug = $this->page['currentCategorySlug'] = $this->property('slug');
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
        $this->categories = $this->page['categories'] = $this->loadCategories();
    }

    /**
     * Load all categories or, depending on the <displayEmpty> option, only those that have blog posts
     * @return mixed
     */
    protected function loadCategories()
    {
        if (!$this->property('displayEmpty')) {
            $categories = Category::withCount('posts')->getNested();

            $categories = $categories->filter(function($item) {
                if ($item->posts_count) {
                    return $item;
                }
            });
        }
        else {
            $categories = Category::withCount('posts')->getNested();
        }

        /*
         * Add a "url" helper attribute for linking to each category
         */
        return $this->linkCategories($categories);
    }

    protected function linkCategories($categories)
    {
        return $categories->each(function($category) {
            $category->setUrl($this->categoryPage, $this->controller);

            if ($category->children) {
                $this->linkCategories($category->children);
            }
        });
    }

}
