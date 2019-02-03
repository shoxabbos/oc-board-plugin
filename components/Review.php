<?php namespace Shohabbos\Board\Components;

use Auth;
use Input;
use Flash;
use Redirect;
use Validator;
use ValidationException;
use Cms\Classes\ComponentBase;
use Shohabbos\Board\Models\Post;
use Shohabbos\Board\Models\Review as ReviewModel;
use Shohabbos\Payme\Models\Settings;


class Review extends ComponentBase
{
    

    public function componentDetails()
    {
        return [
            'name'        => 'Отзывы',
            'description' => 'Отзывы пользователей'
        ];
    }

    public function defineProperties()
    {
        return [
            'userNumber' => [
                'title'       => 'ID пользователя',
                'description' => 'Параметр с помощью которого определить пользователя',
                'type'        => 'string',
                'default'     => '{{ :id }}',
            ]
        ];
    }

    public function loadReviews() {
        return ReviewModel::where('user_id', $this->property('userNumber'))->get();
    }


    // on payment

    public function onCreate() {
        $user = Auth::getUser();
        $data = Input::only(['text']);
        $data['user_id'] = $this->property('userNumber');
        $data['author_id'] = $user ? $user->id : 0;

        $rules = [
            'user_id' => 'required|exists:users,id',
            'author_id' => 'required|exists:users,id',
            'text' => 'required|string|min:5'
        ];

        $validation = Validator::make($data, $rules);

        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        if ($data['user_id'] == $data['author_id']) {
            throw new ValidationException(['message' => 'Вы не сможете себе оставить отзыв']);
        }

        $review = new ReviewModel($data);
        $review->save();

        Flash::success("Отзыв добавлен");
    }




}
