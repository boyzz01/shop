<?php

namespace App\Http\Controllers\Admin;

use App\Models\OlshopLink;
use Illuminate\Http\Request;
use Datatables;

class OlshopLinkController extends AdminBaseController
{
    //*** JSON Request
    public function datatables()
    {
        $datas = OlshopLink::where('user_id', '=', 0)->latest('id')->get();
        //--- Integrating This Collection Into Datatables
        return Datatables::of($datas)
            ->addColumn('status', function (OlshopLink $data) {
                $class = $data->status == 1 ? 'drop-success' : 'drop-danger';
                $s = $data->status == 1 ? 'selected' : '';
                $ns = $data->status == 0 ? 'selected' : '';
                return '<div class="action-list"><select class="process select droplinks ' . $class . '"><option data-val="1" value="' . route('admin-olshoplink-status', ['id1' => $data->id, 'id2' => 1]) . '" ' . $s . '>' . __("Activated") . '</option><<option data-val="0" value="' . route('admin-olshoplink-status', ['id1' => $data->id, 'id2' => 0]) . '" ' . $ns . '>' . __("Deactivated") . '</option>/select></div>';
            })
            ->addColumn('action', function (OlshopLink $data) {
                return '<div class="action-list"><a href="' . route('admin-olshoplink-edit', $data->id) . '"> <i class="fas fa-edit"></i>' . __('Edit') . '</a></div>';
            })
            ->rawColumns(['status', 'action'])
            ->toJson(); //--- Returning Json Data To Client Side
    }

    public function index()
    {
        return view('admin.olshoplink.index');
    }

    public function create()
    {
        return view('admin.olshoplink.create');
    }

    //*** POST Request
    public function store(Request $request)
    {

        //--- Logic Section
        $data = new OlshopLink();
        $input = $request->all();
        $data->fill($input)->save();
        //--- Logic Section Ends

        //--- Redirect Section  
        $msg = __('New Data Added Successfully.') . '<a href="' . route("admin-olshoplink-index") . '">' . __("View Lists") . '</a>';;
        return response()->json($msg);
        //--- Redirect Section Ends  
    }

    //*** GET Request
    public function edit($id)
    {
        $data = OlshopLink::findOrFail($id);
        return view('admin.olshoplink.edit', compact('data'));
    }

    //*** POST Request
    public function update(Request $request, $id)
    {
        //--- Logic Section
        $data = OlshopLink::findOrFail($id);
        $input = $request->all();
        $data->update($input);
        //--- Logic Section Ends

        //--- Redirect Section          
        $msg = __('Data Updated Successfully.') . '<a href="' . route("admin-olshoplink-index") . '">' . __("View Lists") . '</a>';;
        return response()->json($msg);
        //--- Redirect Section Ends  

    }

    //*** GET Request
    public function status($id1, $id2)
    {
        $data = OlshopLink::findOrFail($id1);
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
        $data = OlshopLink::findOrFail($id);
        $data->delete();
        //--- Redirect Section     
        $msg = __('Data Deleted Successfully.');
        return response()->json($msg);
        //--- Redirect Section Ends   
    }
}
