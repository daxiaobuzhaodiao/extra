<?php

namespace App\Http\Requests;

class AddressRequest extends Request
{
  
    public function rules()
    {
        switch($this->method()){
            case 'PATCH':
            case 'POST':
                return [
                    'province'      => 'required',
                    'city'          => 'required',
                    'district'      => 'required',
                    'address'       => 'required',
                    'zip'           => 'required',
                    'contact_name'  => 'required',
                    'contact_phone' => 'required',
                ];
                break;
        }
    }
}
