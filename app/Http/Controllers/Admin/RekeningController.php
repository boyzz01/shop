<?php

namespace App\Http\Controllers\Admin;

use App\Models\Rekening;
use Illuminate\Http\Request;
use Datatables;

class RekeningController extends AdminBaseController
{
    //*** JSON Request
    public function datatables()
    {
        $datas = Rekening::where('user_id', '=', 0)->latest('id')->get();
        //--- Integrating This Collection Into Datatables
        return Datatables::of($datas)
            ->addColumn('status', function (Rekening $data) {
                $class = $data->status == 1 ? 'drop-success' : 'drop-danger';
                $s = $data->status == 1 ? 'selected' : '';
                $ns = $data->status == 0 ? 'selected' : '';
                return '<div class="action-list"><select class="process select droplinks ' . $class . '"><option data-val="1" value="' . route('admin-rekening-status', ['id1' => $data->id, 'id2' => 1]) . '" ' . $s . '>' . __("Activated") . '</option><<option data-val="0" value="' . route('admin-rekening-status', ['id1' => $data->id, 'id2' => 0]) . '" ' . $ns . '>' . __("Deactivated") . '</option>/select></div>';
            })
            ->addColumn('action', function (Rekening $data) {
                return '<div class="action-list"><a href="' . route('admin-rekening-edit', $data->id) . '"> <i class="fas fa-edit"></i>' . __('Edit') . '</a></div>';
            })
            ->rawColumns(['status', 'action'])
            ->toJson(); //--- Returning Json Data To Client Side
    }

    public function index()
    {
        return view('admin.rekening.index');
    }

    public function create()
    {
        return view('admin.rekening.create');
    }

    //*** POST Request
    public function store(Request $request)
    {

        //--- Logic Section
        $data = new Rekening();
        $input = $request->all();
        $data->fill($input)->save();
        //--- Logic Section Ends

        //--- Redirect Section  
        $msg = __('New Data Added Successfully.') . '<a href="' . route("admin-rekening-index") . '">' . __("View Lists") . '</a>';;
        return response()->json($msg);
        //--- Redirect Section Ends  
    }

    //*** GET Request
    public function edit($id)
    {
        $data = Rekening::findOrFail($id);
        return view('admin.rekening.edit', compact('data'));
    }

    //*** POST Request
    public function update(Request $request, $id)
    {
        //--- Logic Section
        $data = Rekening::findOrFail($id);
        $input = $request->all();
        $data->update($input);
        //--- Logic Section Ends

        //--- Redirect Section          
        $msg = __('Data Updated Successfully.') . '<a href="' . route("admin-rekening-index") . '">' . __("View Lists") . '</a>';;
        return response()->json($msg);
        //--- Redirect Section Ends  

    }

    //*** GET Request
    public function status($id1, $id2)
    {
        $data = Rekening::findOrFail($id1);
        $data->status = $id2;
        $data->update();
        //--- Redirect Section
        $msg = __('Status Updated Successfully.');
        return response()->json($msg);
        //--- Redirect Section Ends
    }


    //*** GET Request
    public function destroy($id)
    {
        $data = Rekening::findOrFail($id);
        $data->delete();
        //--- Redirect Section     
        $msg = __('Data Deleted Successfully.');
        return response()->json($msg);
        //--- Redirect Section Ends   
    }
}
