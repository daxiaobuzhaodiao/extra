<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAddress;
use App\Http\Requests\AddressRequest;

class UserAddressesController extends Controller
{
    // 查
    public function index(Request $request)
    {
        return view('user_addresses.index', ['addresses' => $request->user()->addresses]);
    }
    // 返回增加页面
    public function create(UserAddress $address)
    {
        return view('user_addresses.create_and_edit', ['address' => $address]);
    }
    // 增
    public function store(AddressRequest $request)
    {
        $res = $request->user()->addresses()->create($request->all());
        return redirect()->route('user_addresses.index');
    }
    // 返回编辑页面
    public function edit(UserAddress $userAddress)
    {
        $this->authorize('isOwnerOf', $userAddress);
        return view('user_addresses.create_and_edit', ['address' => $userAddress]);
    }
    // 改
    public function update(AddressRequest $request, UserAddress $userAddress)
    {
        $this->authoriza('isOwnerOf', $userAddress);
        $userAddress->update($request->all());
        return redirect()->route('user_addresses.index');
    }
    // 删
    public function destroy(UserAddress $userAddress)
    {
        $this->authorize('isOwnerOf', $userAddress);
        $userAddress->delete();
        return [];
    }
    
}
