<?php

namespace App\Http\Requests;

use App\PostsSource;
use App\Rules\Twitter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PostSourceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @param Request $request
     * @return array
     */
    public function rules(Request $request)
    {
        $tableName = with(new PostsSource)->getTable();
        $id = $request->route('item')->id ?? null;

        return [
            'type' => 'required|string|in:' . PostsSource::SOURCE_TYPE_TWITTER,

            'name' => [
                'required',
                'string',
                'min:2',
                'max:64',
                Rule::unique($tableName)->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })->ignore($id, 'id')
            ],

            'account_name' => [
                'required',
                'string',
                'min:2',
                'max:64',
                Rule::unique($tableName)->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })->ignore($id, 'id'),
                new Twitter(), // Check an twitter account name
            ],
        ];
    }
}
