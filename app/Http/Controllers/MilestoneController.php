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
                    $data = Milestone::select('id', 'name' ,'description', 'amount', 'project_id','deadline' ,'status','payment_status')->get();

                    return Datatables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($data){
                                return ' <div class="btn-group btn-sm">
                                                <a href="'.route('milestones.view', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-eye"></i>
                                                </a> 
                                                <a href="'.route('milestones.edit', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-edit"></i>
                                                </a> 
                                                <a href="javascript:;" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                                    <i class="fa fa-bars"></i>
                                                </a> 
                                                <ul class="dropdown-menu">

                                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="w.i.p." data-id="'.base64_encode($data->id).'">Work In Progress</a></li>
                                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="pending" data-id="'.base64_encode($data->id).'">Pending</a></li>
                                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="complate" data-id="'.base64_encode($data->id).'">Complate</a></li>
                                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="deleted" data-id="'.base64_encode($data->id).'">Delete</a></li>
                                                    
                                                </ul>
                                            </div>';
                            })

                            ->editColumn('status', function($data) {
                                if($data->status == 'pending'){
                                    return '<span class="badge badge-pill badge-danger">Pending</span>';
                                }else if($data->status == 'w.i.p.'){
                                    return '<span class="badge badge-pill badge-warning">Work In Progress</span>';
                                }else if($data->status == 'complate'){
                                     return '<span class="badge badge-pill badge-success">Complate</span>';
                                }else{
                                    return '-';
                                }
                            })

                            ->editColumn('payment_status', function($data) {
                                if($data->payment_status == 'pending'){
                                    return '
                                            <a href="javascript:;" data-toggle="dropdown">
                                                <span class="badge badge-pill badge-danger">Pending</span>
                                            </a>

                                            <ul class="dropdown-menu">

                                                <li><a class="dropdown-item" href="javascript:;" onclick="payment_change_status(this);" data-status="pending" data-id="'.base64_encode($data->id).'">Pending</a></li>
                                                <li><a class="dropdown-item" href="javascript:;" onclick="payment_change_status(this);" data-status="complate" data-id="'.base64_encode($data->id).'">Complate</a></li>
                                            </ul>

                                    ';
                                }else if($data->payment_status == 'complate'){
                                     return '
                                        <a href="javascript:;" data-toggle="dropdown">
                                            <span class="badge badge-pill badge-success">Complate</span>
                                        </a>

                                            <ul class="dropdown-menu">

                                                <li><a class="dropdown-item" href="javascript:;" onclick="payment_change_status(this);" data-status="pending" data-id="'.base64_encode($data->id).'">Pending</a></li>
                                                <li><a class="dropdown-item" href="javascript:;" onclick="payment_change_status(this);" data-status="complate" data-id="'.base64_encode($data->id).'">Complate</a></li>
                                            </ul>';
                                }else{
                                    return '-';
                                }
                            })

                            ->rawColumns(['action', 'status' ,'payment_status'])
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
                    $description = $request->description;
                    $amount = $request->amount;
                    $deadline = $request->deadline;

                    for($i=0; $i<count($name); $i++){
                        $order_detail_crud = [
                                'project_id' => $request->id,
                                'name' => $name[$i],
                                'description' => $description[$i],
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
                    $data = MileStone::where(['id' => $id])
                                    ->first();
                
                    if($data){
                        return view('milestone.edit', ['data' => $data]);
                    }else{
                        return redirect()->back()->with('error', 'No Milestone Found!');
                    }
                }else{
                    return redirect()->back()->with('error', 'Something went wrong!');
                }
                
            }
        /** edit */ 

        /** update */
            public function update(MilestoneRequest $request){
                if($request->ajax()){ return true; }

                $exst_rec = MileStone::where(['id' => $request->id])->first();
                
                if(!empty($request->all())){
                    $crud = [
                            'name' => ucfirst($request->name),
                            'description' => $request->description ?? NULL,
                            'amount' => $request->amount ?? NULL,
                            'deadline' => date('Y-m-d',strtotime($request->deadline)) ?? NULL,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => auth()->user()->id
                    ];

                    DB::beginTransaction();
                    try {
                        DB::enableQueryLog();
                        $update = Milestone::where(['id' => $request->id])->update($crud);
                        if($update){
                            DB::commit();
                            return redirect()->route('milestones' ,['id' => base64_encode($request->project_id)])->with('success', 'Milestone updated successfully.');
                        }else{
                            DB::rollback();
                            return redirect()->back()->with('error', 'Faild to update Milestone!')->withInput();
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return redirect()->back()->with('error', 'Faild to update Milestone!')->withInput();
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
                    $data = MileStone::where(['id' => $id])
                                    ->first();
                
                    if($data){
                        return view('milestone.view', ['data' => $data]);
                    }else{
                        return redirect()->back()->with('error', 'No Milestone Found!');
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

                    $data = MileStone::where(['id' => $id])->first();

                    if(!empty($data)){
                        if($status == 'deleted')
                            $update = MileStone::where(['id' => $id])->delete();
                        else
                            $update = MileStone::where(['id' => $id])->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth()->user()->id]);
                        
                        if($update){
                            return response()->json(['code' => 200]);
                        }else{
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


        /** Payment change-status */
            public function payment_change_status(Request $request){
                if(!$request->ajax()){ exit('No direct script access allowed'); }

                if(!empty($request->all())){
                    $id = base64_decode($request->id);
                    $status = $request->status;

                    $data = MileStone::where(['id' => $id])->first();

                    if(!empty($data)){
                        if($status == 'deleted')
                            $update = MileStone::where(['id' => $id])->delete();
                        else
                            $update = MileStone::where(['id' => $id])->update(['payment_status' => $status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth()->user()->id]);
                        
                        if($update){
                            return response()->json(['code' => 200]);
                        }else{
                          return response()->json(['code' => 201]);
                        }
                    }else{
                        return response()->json(['code' => 201]);
                    }
                }else{
                    return response()->json(['code' => 201]);
                }
            }
        /** Payment change-status */
    }