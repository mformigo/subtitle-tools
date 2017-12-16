<?php

namespace App\Http\Requests;

use App\Http\Rules\FileNotEmptyRule;
use App\Http\Rules\SupRule;
use Illuminate\Foundation\Http\FormRequest;

class SupRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'subtitle'    => ['bail', 'required', 'file', new FileNotEmptyRule, new SupRule],
            'ocrLanguage' => 'required|in:'.implode(',', config('st.tesseract.languages')),
        ];
    }

    public function getSupFile()
    {
        return $this->file('subtitle');
    }

    public function getOcrLanguage()
    {
        return $this->get('ocrLanguage');
    }
}
