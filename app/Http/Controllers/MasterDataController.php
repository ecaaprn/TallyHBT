<?php

namespace App\Http\Controllers;

use App\Models\MasterKapal;
use App\Models\MasterTruck;
use App\Models\User;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function index(Request $request)
    {
        $allKapals = MasterKapal::orderBy('nama', 'asc')->get();

        $search_kapal = $request->query('search_kapal');
        $kapals = MasterKapal::when($search_kapal, function ($query, $search) {
                return $query->where('nama', 'like', '%' . $search . '%');
            })
            ->orderBy('id', 'asc')
            ->paginate(10, ['*'], 'kapal_page')
            ->withQueryString();

        $search_truck = $request->query('search_truck');
        $trucks = MasterTruck::with('kapal')
            ->when($search_truck, function ($query, $search) {
                return $query->where('nama', 'like', '%' . $search . '%');
            })
            ->orderBy('id', 'asc')
            ->paginate(10, ['*'], 'truck_page')
            ->withQueryString();

        $search_user = $request->query('search_user');
        $users = User::when($search_user, function ($query, $search) {
                return $query->where('nama', 'like', '%' . $search . '%')
                             ->orWhere('email', 'like', '%' . $search . '%');
            })
            ->orderBy('id', 'asc')
            ->paginate(10, ['*'], 'user_page')
            ->withQueryString();

        return view('master_data.index', compact('kapals', 'trucks', 'users', 'allKapals'));
    }
}
