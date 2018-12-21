<?php namespace Shohabbos\Board\Components;

use Input;
use Redirect;
use Validator;
use Cms\Classes\Page;
use ValidationException;
use Cms\Classes\ComponentBase;
use Shohabbos\Board\Models\Category;

class Breadcrumb extends ComponentBase
{   

    /**
     * Reference to the page name for linking to categories.
     * @var string
     */
    public $categoryPage;

    public function componentDetails()
    {
        return [
            'name'        => 'shohabbos.board::lang.settings.breadcrumb_title',
            'description' => 'shohabbos.board::lang.settings.breadcrumb_description'
        ];
    }

    public function defineProperties()
    {
        return [
            'activeCategory' => [
                'title'       => 'shohabbos.board::lang.settings.active_category',
                'description' => 'shohabbos.board::lang.settings.active_category_description',
                'type'        => 'string',
                'default'     => '{{ :category }}'
            ],
            'categoryPage' => [
                'title'       => 'shohabbos.board::lang.settings.posts_category',
                'description' => 'shohabbos.board::lang.settings.posts_category_description',
                'type'        => 'dropdown',
                'default'     => 'blog/category',
                'group'       => 'Links',
            ],
        ];
    }


    public function onRun() {
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
        $this->page['category'] = $category = $this->loadCategory();
        
        if ($category) {
            $categories = $this->loadCategories($category);
            $categories->each(function($category) {
                $category->setUrl($this->categoryPage, $this->controller);
            });

            $this->page['categories'] = $categories;    
        }
    }

    protected function loadCategories(Category $category = null) {
        if ($category->children->count()) {
            return $category->children()->withCount('posts')->get();
        }

        if ($category->parent->children->count()) {
            return $category->parent->children()->withCount('posts')->get();
        }

        return Category::withCount('posts')->getNested();
    }

    protected function loadCategory()
    {
        if (!$slug = $this->property('activeCategory')) {
            return null;
        }

        $category = new Category;

        $category = $category->isClassExtendedWith('RainLab.Translate.Behaviors.TranslatableModel')
            ? $category->transWhere('slug', $slug)
            : $category->where('slug', $slug);

        $category = $category->first();

        if ($category) {
            $category->setUrl($this->categoryPage, $this->controller);

            if ($category->parent) {
                $category->parent->setUrl($this->categoryPage, $this->controller);
            }
        }



        return $category ?: null;
    }


    public function getCategoryPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

}
