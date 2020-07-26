<?php

namespace App\Http\Controllers;

use App\Role;
use App\BaseModel;
use App\OurFeePolicyDocument;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;

class OurFeePolicyDocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:our_fee_policy_document_create'])->only(['create','store']);
        $this->middleware(['permission:our_fee_policy_document_edit'])->only(['edit','update']);
        $this->middleware(['permission:our_fee_policy_document_delete'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $ourFeePolicyDocumentModel = new OurFeePolicyDocument();
        $isFiltered                = false;

        if (count($request->all()) > 0) {
            $isFiltered = (!empty(array_filter($request->all())));
        }

        $ourFeePolicyDocuments = $ourFeePolicyDocumentModel::query();

        if ($isFiltered) {
            if ($request->get('s', false)) {
                $s = $request->get('s');

                $ourFeePolicyDocuments->where(function($query) use($s) {
                    $query->where('title','LIKE',"%$s%")
                          ->orWhere('text','LIKE',"%$s%");
                });
            }
        }

        $ourFeePolicyDocuments = $ourFeePolicyDocuments->where('is_removed', BaseModel::$notRemoved)->paginate(20);

        return view('app.our_fee_policy_document.list', ['ourFeePolicyDocuments' => $ourFeePolicyDocuments, 'term' => $request, 'isFiltered' => $isFiltered]);
    }

    public function create()
    {
        return view('app.our_fee_policy_document.create');
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'title' => ['required', 'string', 'max:255'],
            'text'  => ['nullable', 'string', 'max:255']
        ]);

        $validator->validate();

        $ourFeePolicyDocument = OurFeePolicyDocument::create([
            'title' => $data['title'],
            'text'  => $data['text']
        ]);

        if ($ourFeePolicyDocument) {
            return redirect('our_fee_policy_document')->with('success', __("Our Fee Policy Document created!"));
        }

        return redirect('our_fee_policy_document/create')->with('error', __("There has been an error!"));
    }

    public function edit(int $id)
    {
        $ourFeePolicyDocument = OurFeePolicyDocument::find($id);

        if ($ourFeePolicyDocument) {
            return view('app.our_fee_policy_document.edit', ['ourFeePolicyDocument' => $ourFeePolicyDocument]);
        }

        return redirect('our_fee_policy_document')->with('error', __("Not found!"));
    }

    public function update(Request $request, int $id)
    {
        $ourFeePolicyDocument = OurFeePolicyDocument::find($id);

        if ($ourFeePolicyDocument) {
            $data = $request->all();

            $validator = Validator::make($data, [
                'title' => ['required', 'string', 'max:255'],
                'text'  => ['nullable', 'string', 'max:255']
            ]);

            $validator->validate();

            if ($ourFeePolicyDocument->update($data)) {
                return redirect('our_fee_policy_document')->with('success', __("Our Fee Policy Document updated!"));
            } else {
                return redirect('our_fee_policy_document')->with('error', __("There has been an error!"));
            }
        }

        return redirect('our_fee_policy_document')->with('error', __("Not found!"));
    }
 
    public function destroy(int $id)
    {
        $ourFeePolicyDocument = OurFeePolicyDocument::find($id);

        if ($ourFeePolicyDocument) {
            $isRemoved = $ourFeePolicyDocument->update(['is_removed' => BaseModel::$removed]);

            if ($isRemoved) {
                return redirect('our_fee_policy_document')->with('success', __("Our Fee Policy Document deleted!"));
            } else {
                return redirect('our_fee_policy_document')->with('error', __("There has been an error!"));
            }
        }

        return redirect('our_fee_policy_document')->with('error', __("Not found!"));
    }
}
