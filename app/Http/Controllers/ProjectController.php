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
                                                    <li><a class="dropdown-item" href="'.route('milestones',['id' => base64_encode($data->id)]).'">Milestones</a></li>
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
                dd($request->all());
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

                    $data = Project::where(['id' => $id])->first();

                    if(!empty($data)){
                        if($status == 'deleted')
                            $update = Project::where(['id' => $id])->delete();
                        else
                            $update = Project::where(['id' => $id])->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth()->user()->id]);
                        
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

        /** MileStone */
            public function milestone(Request $request){
                $id = base64_decode($request->id);

                if($id){
                    return view('milestones',['id' => $id]);
                }else{
                    return redirect()->back()->with('error' , 'Something Went Wrong !');
                }
            }

            public function milestone_edit(Request $request){
                $id = $request->id;

                if($id){
                    for($i=0; $i<count($$request->name); $i++){
                                $milestone_crud = [
                                        'project_id' => $request->id,
                                        'name' => $name[$i],
                                        'amount' => $amount[$i],
                                        'deadline' => date('Y-m-d' , strtotime($deadline[$i])),
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'created_by' => auth()->user()->id,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => auth()->user()->id
                                ];

                                MileStone::insertGetId($milestone_crud);
                            }

                }else{
                    return redirect()->back()->with('error' , 'Something Went Wrong !');
                }
            }
        /** MileStone */ 
    }