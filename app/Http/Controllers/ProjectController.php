<?php    

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\Project;
    use App\Http\Requests\ProjectRequest;
    use Auth, Validator, File, DB, DataTables;

    class ProjectController extends Controller{
        /** index */
            public function index(Request $request){
                if($request->ajax()){
                    $data = Project::select('id', 'title', 'client_name', 'budget','deadline' ,'status')->get();

                    return Datatables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($data){
                                return ' <div class="btn-group btn-sm">
                                                <a href="" class="btn btn-default btn-xs">
                                                    <i class="fa fa-eye"></i>
                                                </a> 
                                                <a href="" class="btn btn-default btn-xs">
                                                    <i class="fa fa-edit"></i>
                                                </a> 
                                                <a href="javascript:;" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                                    <i class="fa fa-bars"></i>
                                                </a> 
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="active" data-old_status="'.$data->status.'" data-id="'.base64_encode($data->id).'">Active</a></li>
                                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="inactive" data-old_status="'.$data->status.'" data-id="'.base64_encode($data->id).'">Inactive</a></li>
                                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="deleted" data-old_status="'.$data->status.'" data-id="'.base64_encode($data->id).'">Delete</a></li>
                                                    
                                                </ul>
                                            </div>';
                            })

                            ->editColumn('status', function($data) {
                                if($data->status == 'pending'){
                                    return '<span class="badge badge-pill badge-danger">Pending</span>';
                                }else if($data->status == 'wip'){
                                    return '<span class="baige badge-pill badge-warning">W.I.P.</span>';
                                }else if($data->status == 'complate'){
                                    return '<span class="baige badge-pill badge-success">Complate</span>';
                                }else{
                                    return '-';
                                }
                            })

                            ->rawColumns(['action', 'status'])
                            ->make(true);
                }

                return view('project.index');
            }
        /** index */

        /** create */
            public function create(Request $request){
                return view('project.create');
            }
        /** create */

        /** insert */
            public function insert(ProjectRequest $request){
                if($request->ajax()){ return true; }

                if(!empty($request->all())){
                    $crud = [
                            'title' => ucfirst($request->title),
                            'client_name' => $request->client_name,
                            'description' => $request->description,
                            'budget' => $request->budget,
                            'deadline' => date('Y-m-d',strtotime($request->deadline)),
                            'payment' => 'pending',
                            'status' => 'pending',
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => auth()->user()->id,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => auth()->user()->id
                    ];

                    DB::beginTransaction();
                    try {
                        $last_id = Project::insertGetId($crud);
                        
                        if($last_id){
                            DB::commit();
                            return redirect()->route('projects')->with('success', 'Project created successfully.');
                        }else{
                            DB::rollback();
                            return redirect()->back()->with('error', 'Faild to create Project!')->withInput();
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return redirect()->back()->with('error', 'Faild to create Project!')->withInput();
                    }
                }else{
                    return redirect()->back()->with('error', 'Something went wrong')->withInput();
                }
            }
        /** insert */

        /** edit */
            public function edit(Request $request, $id=''){
                if($id == '')
                    return redirect()->back()->with('error', 'Something went wrong');

                $id = base64_decode($id);

                $path = URL('/uploads/inventory').'/';
                $generate = $this->generate($id);
                if($generate){
                    $data = Inventory::select('id', 'title', 'qrcode', 'description',
                                            DB::Raw("CASE
                                                WHEN ".'file'." != '' THEN CONCAT("."'".$path."'".", ".'file'.")
                                                ELSE CONCAT("."'".$path."'".", 'default.png')
                                                END as image"))
                                    ->where(['id' => $id])
                                    ->first();
                
                    if($data){
                        $details = InventoryDetail::
                                                    select('id', 'name', 'quantity')
                                                    ->where(['inventory_id' => $data->id])
                                                    ->get();

                        if($details->isNotEmpty())
                            $data->details = $details;
                        else
                            $data->details = collect();

                        return view('inventory.edit', ['data' => $data]);
                    }else{
                        return redirect()->back()->with('error', 'No inventory found');
                    }
                }else{
                    return redirect()->back()->with('error', 'Something went wrong');
                }
                
            }
        /** edit */ 

        /** update */
            public function update(InventoryRequest $request){
                if($request->ajax()){ return true; }

                $exst_rec = Inventory::where(['id' => $request->id])->first();
                
                if(!empty($request->all())){
                    $crud = [
                            'title' => ucfirst($request->title),
                            'description' => $request->description ?? NULL,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => auth()->user()->id
                    ];

                    if(!empty($request->file('image'))){
                        $file = $request->file('image');
                        $filenameWithExtension = $request->file('image')->getClientOriginalName();
                        $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
                        $extension = $request->file('image')->getClientOriginalExtension();
                        $filenameToStore = time()."_".$filename.'.'.$extension;

                        $folder_to_upload = public_path().'/uploads/inventory/';

                        if (!\File::exists($folder_to_upload)) {
                            \File::makeDirectory($folder_to_upload, 0777, true, true);
                        }

                        $crud["file"] = $filenameToStore;
                    }else{
                        $crud["file"] = $exst_rec->file;
                    }

                    DB::beginTransaction();
                    try {
                        $update = Inventory::where(['id' => $request->id])->update($crud);
                    
                        if($update){
                            if(!empty($request->file('image'))){
                                $file->move($folder_to_upload, $filenameToStore);
                            }
                            $name = $request->name;
                            $quantity = $request->quantity;

                            for($i=0; $i<count($name); $i++){
                                $exst_detail = InventoryDetail::select('id')->where(['inventory_id' => $request->id, 'name' => $name[$i]])->first();

                                if(!empty($exst_detail)){
                                    $detail_crud = [
                                            'inventory_id' => $request->id,
                                            'name' => $name[$i],
                                            'quantity' => $quantity[$i],
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'updated_by' => auth()->user()->id
                                    ];

                                    InventoryDetail::where(['id' => $exst_detail->id])->update($detail_crud);
                                }else{
                                    $detail_crud = [
                                            'inventory_id' => $request->id,
                                            'name' => $name[$i],
                                            'quantity' => $quantity[$i],
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'created_by' => auth()->user()->id,
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'updated_by' => auth()->user()->id
                                    ];

                                    InventoryDetail::insertGetId($detail_crud);
                                }
                            }

                            DB::commit();
                            return redirect()->route('inventories')->with('success', 'inventory updated successfully.');
                        }else{
                            DB::rollback();
                            return redirect()->back()->with('error', 'Faild to update inventory!')->withInput();
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return redirect()->back()->with('error', 'Faild to update inventory!')->withInput();
                    }
                }else{
                    return redirect()->back()->with('error', 'Something went wrong')->withInput();
                }
            }
        /** update */

        /** view */
            public function view(Request $request, $id=''){
                if($id == '')
                    return redirect()->back()->with('error', 'Something went wrong');

                $id = base64_decode($id);

                $generate = $this->generate($id);

                if($generate){
                    $data = Inventory::select('id', 'title', 'qrcode', 'description')->where(['id' => $id])->first();
                
                    if($data){
                        $details = InventoryDetail::select('id', 'name', 'quantity')->where(['inventory_id' => $data->id])->get();

                        if($details->isNotEmpty())
                            $data->details = $details;
                        else
                            $data->details = collect();

                        return view('inventory.view', ['data' => $data]);
                    }else{
                        return redirect()->back()->with('error', 'No inventory found');
                    }
                }else{
                    return redirect()->back()->with('error', 'Something went wrong');
                }
                    
            }
        /** view */ 

        /** delete-detail */
            public function delete_detail(Request $request){
                if(!$request->ajax()){ exit('No direct script access allowed'); }

                if(!empty($request->all())){
                    $id = $request->id;

                    $data = InventoryDetail::where(['id' => $id])->first();

                    if(!empty($data)){
                        $update = InventoryDetail::where(['id' => $id])->delete();                        
                        
                        if($update)
                            return response()->json(['code' => 200]);
                        else
                            return response()->json(['code' => 201]);
                    }else{
                        return response()->json(['code' => 201]);
                    }
                }else{
                    return response()->json(['code' => 201]);
                }
            }
        /** delete-detail */

        /** change-status */
            public function change_status(Request $request){
                if(!$request->ajax()){ exit('No direct script access allowed'); }

                if(!empty($request->all())){
                    $id = base64_decode($request->id);
                    $status = $request->status;

                    $data = Inventory::where(['id' => $id])->first();

                    if(!empty($data)){
                        if($status == 'deleted')
                            $update = Inventory::where(['id' => $id])->delete();
                        else
                            $update = Inventory::where(['id' => $id])->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth()->user()->id]);
                        
                        if($update){
                            InventoryDetail::where(['inventory_id' => $id])->delete();

                            $exst_qrcode = public_path().'/uploads/qrcodes/'.$data->qrcode;
                            $exst_file = public_path().'/uploads/inventory/'.$data->image;

                            if(\File::exists($exst_qrcode) && $exst_qrcode != '')
                                @unlink($exst_qrcode);

                            if(\File::exists($exst_file) && $exst_file != '')
                                @unlink($exst_file);
                            
                            return response()->json(['code' => 200]);
                        }else{
                            InventoryDetail::where(['inventory_id' => $id])->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth()->user()->id]);
                            
                            return response()->json(['code' => 201]);
                        }
                    }else{
                        return response()->json(['code' => 201]);
                    }
                }else{
                    return response()->json(['code' => 201]);
                }
            }
        /** change-status */

        /** generate-qrcode */
            public function generate($id=''){
                if($id == '')
                    return false;

                $exst_file = public_path().'/uploads/qrcodes/qrcode_'.$id.'.png';
                if(\File::exists($exst_file) && $exst_file != '')
                    @unlink($exst_file);
                
                $qrname = 'qrcode_'.$id.'.png';

                \QrCode::size(500)->format('png')->merge('/public/qr_logo.png', .3)->generate($id, public_path('uploads/qrcodes/'.$qrname));

                $update = Inventory::where(['id' => $id])->update(['qrcode' => $qrname, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth()->user()->id]);
                
                if($update)
                    return true;
                else
                    return false;
            }
        /** generate-qrcode */

        /** generate-item-qrcode */
            public function generate_item($id=''){
                if($id == '')
                    return false;
                $folder_to_uploads = public_path().'/uploads/qrcodes/item/';

                if (!\File::exists($folder_to_uploads)) {
                    \File::makeDirectory($folder_to_uploads, 0777, true, true);
                }

                $exst_file = public_path().'/uploads/qrcodes/item/qrcode_'.$id.'.png';
                if(\File::exists($exst_file) && $exst_file != '')
                    @unlink($exst_file);
                
                $qrname = 'qrcode_'.$id.'.png';

                \QrCode::size(500)->format('png')->merge('/public/qr_logo.png', .3)->generate($id, public_path('uploads/qrcodes/item/'.$qrname));

                $update = InventoryDetail::where(['id' => $id])->update(['qr_code' => $qrname, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth()->user()->id]);
                
                if($update)
                    return true;
                else
                    return false;
            }
        /** generate-item-qrcode */

        /** print-qrcode */
            public function print(Request $request, $id=''){
                if($id == '')
                    return redirect()->back()->with('error', 'something went wrong');

                $id = base64_decode($id);
                $generate = $this->generate($id);
                if($generate){
                    $data = Inventory::select('qrcode')->where(['id' => $id])->first();
                
                    if($data)
                        return view('inventory.print', ['data' => $data]);
                    else
                        return redirect()->back()->with('error', 'Something went wrong');    
                }else{
                    return redirect()->back()->with('error', 'something went wrong');
                }
                
            }
        /** print-qrcode */

        /** print-item-qrcode */
            public function print_item(Request $request, $id=''){
                if($id == '')
                    return redirect()->back()->with('error', 'something went wrong');
                $id = base64_decode($id);
                $generate = $this->generate_item($id);

                if($generate){
                    $data = InventoryDetail::select('qr_code')->where(['id' => $id])->first();
                
                    if($data)
                        return view('inventory.printItem', ['data' => $data]);
                    else
                        return redirect()->back()->with('error', 'Something went wrong');    
                }else{
                    return redirect()->back()->with('error', 'something went wrong');
                }                
            }
        /** print-item-qrcode */

        /** profile remove */
            public function profile_remove(Request $request){
                if(!$request->ajax()){ exit('No direct script access allowed'); }

                if(!empty($request->all())){
                    $id = base64_decode($request->id);
                    $data = DB::table('inventories')->find($id);

                    if($data){
                        if($data->file != ''){
                            $file_path = public_path().'/uploads/inventory/'.$data->file;

                            if(File::exists($file_path) && $file_path != ''){
                                if($data->file != 'default.png'){
                                    unlink($file_path);
                                }
                            }

                            $update = DB::table('inventories')->where(['id' => $id])->limit(1)->update(['file' => null]);

                            if($update)
                                return response()->json(['code' => 200]);
                            else
                                return response()->json(['code' => 201]);
                        }else{
                            return response()->json(['code' => 200]);
                        }
                    }else{
                        return response()->json(['code' => 201]);
                    }
                }else{
                    return response()->json(['code' => 201]);
                }
            }
        /** profile remove */
    }