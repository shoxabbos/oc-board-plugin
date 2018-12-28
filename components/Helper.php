<?php namespace Shohabbos\Board\Components;

use Auth;
use Input;
use Redirect;
use Validator;
use ValidationException;
use Cms\Classes\ComponentBase;
use Shohabbos\Board\Models\Post;
use Shohabbos\Payme\Models\Settings;


class Helper extends ComponentBase
{
    
    public $posts;

    public function componentDetails()
    {
        return [
            'name'        => 'shohabbos.board::lang.settings.helper_title',
            'description' => 'shohabbos.board::lang.settings.helper_description'
        ];
    }



    // on payment

    public function onPay() {
        $user = Auth::getUser();
        $data = Input::only(['sum', 'payment']);
        $data['user_id'] = $user ? $user->id : null;

        $rules = [
            'sum' => 'required|integer|min:1000|max:1000000',
            'payment' => 'required|string|in:click,payme',
            'user_id' => 'required|exists:users,id'
        ];

        $validation = Validator::make($data, $rules);

        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        if ($data['payment'] == 'payme') {
            return $this->methodPayme($data['sum'], $data['user_id']);
        } elseif ($data['payment' == 'click']) {
            return $this->methodClick($data['sum'], $data['user_id']);
        }
        

        throw new ValidationException(['message' => "Payment method not found"]);
    }


    private function methodPayme($amount, $orderId) {
        $data = [
            'm' => Settings::get('merchant_id'),
            'ac.user_id' => $orderId,
            'a' => $amount * 100,
            'l' => 'ru',
        ];

        return Redirect::to('https://checkout.paycom.uz/'.base64_encode(http_build_query($data, '', ';')));
    }

    private function methodClick($amount, $orderId) {
        
    }



}
