<?php namespace Shohabbos\Board\Components;

use Input;
use Redirect;
use Validator;
use ValidationException;
use Cms\Classes\ComponentBase;
use Shohabbos\Board\Models\Location;

class SearchForm extends ComponentBase
{
    
    public $posts;

    public function componentDetails()
    {
        return [
            'name'        => 'shohabbos.board::lang.settings.search_title',
            'description' => 'shohabbos.board::lang.settings.search_description'
        ];
    }

    public function onRun() {
        $this->page['locations'] = Location::withCount('posts')->getNested();
    }


}
