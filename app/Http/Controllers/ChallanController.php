<?php

namespace App\Http\Controllers;

use App\Models\Challan;
use Illuminate\Http\Request;

class ChallanController extends Controller
{
    public function index(){
        $challans = Challan::all();
        return view('admin.challan.index', compact('challans'));
    }

    public function create(){

    }
}
