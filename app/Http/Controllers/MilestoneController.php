<?php    

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\Project;
    use App\Models\Milestone;
    use App\Http\Requests\MilestoneRequest;
    use Auth, Validator, File, DB, DataTables;

    class MilestoneController extends Controller{
        /** index */
            public function index(Request $request){
                if($request->ajax()){
                    $data = Milestone::select('id', 'name' ,'description', 'amount', 'project_id','deadline' ,'status')->get();

                    return Datatables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($data){
                                return ' <div class="btn-group btn-sm">
                                                <a href="'.route('projects.view', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-eye"></i>
                                                </a> 
                                                <a href="'.route('projects.edit', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
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

                return view('milestone.index')->with('id' ,$request->id);
            }
        /** index */

        /** create */
            public function create(Request $request){
                return view('milestone.create')->with('id' ,$request->id);
            }
        /** create */

        /** insert */
            public function insert(MilestoneRequest $request){
                if($request->ajax()){ return true; }

                if(!empty($request->all())){
                    $name = $request->name;
                    $amount = $request->amount;
                    $deadline = $request->deadline;

                    for($i=0; $i<count($name); $i++){
                        $order_detail_crud = [
                                'project_id' => $request->id,
                                'name' => $name[$i],
                                'amount' => $amount[$i],
                                'deadline' => $deadline[$i],
                                'created_at' => date('Y-m-d H:i:s'),
                                'created_by' => auth()->user()->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => auth()->user()->id
                        ];

                    }

                    DB::beginTransaction();
                    try {
                       $last_id = MileStone::insertGetId($order_detail_crud);
                        if($last_id){
                            DB::commit();
                            return redirect()->route('milestones' ,['id' => base64_encode($request->id)])->with('success', 'Milestone created successfully.');
                        }else{
                            DB::rollback();
                            return redirect()->back()->with('error', 'Faild to create Milestone!')->withInput();
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return redirect()->back()->with('error', 'Somthing Went Wrong!')->withInput();
                    }
                }else{
                    return redirect()->back()->with('error', 'Field Are Require')->withInput();
                }
            }
        /** insert */

        /** edit */
            public function edit(Request $request){
                
                $id = base64_decode($request->id);

                if($id){
                    $data = Project::select('id', 'title', 'client_name', 'description','budget' ,'deadline')
                                    ->where(['id' => $id])
                                    ->first();
                
                    if($data){
                        return view('project.edit', ['data' => $data]);
                    }else{
                        return redirect()->back()->with('error', 'No Project Found!');
                    }
                }else{
                    return redirect()->back()->with('error', 'Something went wrong!');
                }
                
            }
        /** edit */ 

        /** update */
            public function update(ProjectRequest $request){
                if($request->ajax()){ return true; }

                $exst_rec = Project::where(['id' => $request->id])->first();
                
                if(!empty($request->all())){
                    $crud = [
                            'title' => ucfirst($request->title),
                            'client_name' => $request->client_name ?? NULL,
                            'description' => $request->description ?? NULL,
                            'budget' => $request->budget ?? NULL,
                            'deadline' => date('Y-m-d',strtotime($request->deadline)) ?? NULL,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => auth()->user()->id
                    ];

                    DB::beginTransaction();
                    try {
                        DB::enableQueryLog();
                        $update = Project::where(['id' => $request->id])->update($crud);
                        // dd(DB::getQueryLog());
                        if($update){
                            DB::commit();
                            return redirect()->route('projects')->with('success', 'Project updated successfully.');
                        }else{
                            DB::rollback();
                            return redirect()->back()->with('error', 'Faild to update Project!')->withInput();
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return redirect()->back()->with('error', 'Faild to update Projects!')->withInput();
                    }
                }else{
                    return redirect()->back()->with('error', 'Something went wrong!')->withInput();
                }
            }
        /** update */

        /** view */
            public function view(Request $request, $id=''){
                $id = base64_decode($request->id);

                if($id){
                    $data = Project::select('id', 'title', 'client_name', 'description','budget' ,'deadline')
                                    ->where(['id' => $id])
                                    ->first();
                
                    if($data){
                        return view('project.view', ['data' => $data]);
                    }else{
                        return redirect()->back()->with('error', 'No Project Found!');
                    }
                }else{
                    return redirect()->back()->with('error', 'Something went wrong!');
                }
                    
            }
        /** view */ 

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
    }