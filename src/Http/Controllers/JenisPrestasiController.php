<?php

namespace Bantenprov\Prestasi\Http\Controllers;

/* Require */
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Bantenprov\Prestasi\Facades\PrestasiFacade;

/* Models */
use Bantenprov\Prestasi\Models\Bantenprov\Prestasi\JenisPrestasi;
use App\User;

/* Etc */
use Validator;

/**
 * The PrestasiController class.
 *
 * @package Bantenprov\Prestasi
 * @author  bantenprov <developer.bantenprov@gmail.com>
 */
class JenisPrestasiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $jenis_prestasi;
    protected $user;

    public function __construct(JenisPrestasi $jenis_prestasi, User $user)
    {
        $this->jenis_prestasi = $jenis_prestasi;
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (request()->has('sort')) {
            list($sortCol, $sortDir) = explode('|', request()->sort);

            $query = $this->jenis_prestasi->orderBy($sortCol, $sortDir);
        } else {
            $query = $this->jenis_prestasi->orderBy('id', 'asc');
        }

        if ($request->exists('filter')) {
            $query->where(function($q) use($request) {
                $value = "%{$request->filter}%";
                $q->where('user_id', 'like', $value)
                    ->orWhere('nama_jenis_prestasi', 'like', $value);
            });
        }

        $perPage = request()->has('per_page') ? (int) request()->per_page : null;
        $response = $query->paginate($perPage);

        foreach($response as $user){
            array_set($response->data, 'user', $user->user->name);
        }

        return response()->json($response)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        $users = $this->user->all();

        foreach($users as $user){
            array_set($user, 'label', $user->name);
        }

        $response['user'] = $users;
        $response['status'] = true;

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Prestasi  $prestasi
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $jenis_prestasi = $this->jenis_prestasi;

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'nama_jenis_prestasi' => 'required|max:255',
        ]);

        if($validator->fails()){
            $check = $jenis_prestasi->where('nama_jenis_prestasi',$request->nama_jenis_prestasi)->whereNull('deleted_at')->count();

            if ($check > 0) {
                $response['message'] = 'Failed, nama_jenis_prestasi ' . $request->nama_jenis_prestasi . ' already exists';
            } else {
                $jenis_prestasi->user_id = $request->input('user_id');
                $jenis_prestasi->nama_jenis_prestasi = $request->input('nama_jenis_prestasi');
                $jenis_prestasi->save();

                $response['message'] = 'success';
            }
        } else {
                $jenis_prestasi->user_id = $request->input('user_id');
                $jenis_prestasi->nama_jenis_prestasi = $request->input('nama_jenis_prestasi');
                $jenis_prestasi->save();

            $response['message'] = 'success';
        }

        $response['status'] = true;

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $jenis_prestasi = $this->jenis_prestasi->findOrFail($id);
        
        $response['user'] = $jenis_prestasi->user;
        $response['jenis_prestasi'] = $jenis_prestasi;
        $response['status'] = true;

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Prestasi  $prestasi
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        $jenis_prestasi = $this->jenis_prestasi->findOrFail($id);

        array_set($jenis_prestasi->user, 'label', $jenis_prestasi->user->name);

        $response['jenis_prestasi'] = $jenis_prestasi;
        $response['user'] = $jenis_prestasi->user;
        $response['status'] = true;

        return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Prestasi  $prestasi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $jenis_prestasi = $this->jenis_prestasi->findOrFail($id);

        if ($request->input('nama_jenis_prestasi') == $request->input('nama_jenis_prestasi'))
        {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'nama_jenis_prestasi' => 'required|max:255',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'nama_jenis_prestasi' => 'required|max:255',
            ]);
        }

        if ($validator->fails()) {
            $check = $jenis_prestasi->where('nama_jenis_prestasi',$request->nama_jenis_prestasi)->whereNull('deleted_at')->count();

            if ($check > 0) {
                $response['message'] = 'Failed, nama_jenis_prestasi ' . $request->nama_jenis_prestasi . ' already exists';
            } else {
                $jenis_prestasi->user_id = $request->input('user_id');
                $jenis_prestasi->nama_jenis_prestasi = $request->input('nama_jenis_prestasi');
                $jenis_prestasi->save();

                $response['message'] = 'success';
            }
        } else {
                $jenis_prestasi->user_id = $request->input('user_id');
                $jenis_prestasi->nama_jenis_prestasi = $request->input('nama_jenis_prestasi');
                $jenis_prestasi->save();

            $response['message'] = 'success';
        }

        $response['status'] = true;

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Prestasi  $prestasi
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $jenis_prestasi = $this->jenis_prestasi->findOrFail($id);

        if ($jenis_prestasi->delete()) {
            $response['status'] = true;
        } else {
            $response['status'] = false;
        }

        return json_encode($response);
    }
}