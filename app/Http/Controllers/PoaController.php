<?php

namespace App\Http\Controllers;

use App\Role;
use App\BaseModel;
use App\PoaAgreement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Http\UploadedFile;

class PoaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:poa_view'])->only(['index']);
        $this->middleware(['permission:poa_create'])->only(['create','store']);
        $this->middleware(['permission:poa_edit'])->only(['edit','update']);
        $this->middleware(['permission:poa_delete'])->only('destroy');
        $this->middleware(['permission:poa_download'])->only('download');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $poaAgreementModel = new PoaAgreement();
        $isFiltered        = false;

        if (count($request->all()) > 0) {
            $isFiltered = (!empty(array_filter($request->all())));
        }

        $poaAgreements = $poaAgreementModel::query();

        if ($isFiltered) {
            if ($request->get('s', false)) {
                $s = $request->get('s');

                $poaAgreements->where(function($query) use($s) {
                    $query->where('title','LIKE',"%$s%")
                          ->orWhere('text','LIKE',"%$s%");
                });
            }
        }

        $poaAgreements = $poaAgreements->where('is_removed', BaseModel::$notRemoved)->paginate(20);

        return view('app.poa.list', ['poaAgreements' => $poaAgreements, 'term' => $request, 'isFiltered' => $isFiltered]);
    }

    public function create()
    {
        return view('app.poa.create');
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'title' =>['required', 'string'],
            'text'  => ['nullable', 'string'],
            'file'  => ['nullable']
        ]);

        $validator->validate();

        $poaAgreement = PoaAgreement::create([
            'title' => $data['title'],
            'text'  => $data['text']
        ]);

        $storeFile = false;
        if ($poaAgreement) {
            $id   = $poaAgreement->id;
            $file = $request->file('file', false);

            if (!empty($id) && $file instanceof UploadedFile) {
                $pathInfos = pathinfo($file->getClientOriginalName());

                if (!empty($pathInfos['extension'])) {
                    $fileName  = (empty($pathInfos['filename']) ? time() : $pathInfos['filename']) . '_' . $id . '_' . '.' . $pathInfos['extension'];

                    $storeFile = $file->storeAs(PoaAgreement::$storageFolderName, $fileName, PoaAgreement::$fileSystem);

                    if ($storeFile) {
                        PoaAgreement::find($id)->update(['file' => $fileName]);
                    }
                }
            }

            if ($storeFile) {
                return redirect('poa')->with('success', __("POA Agreement created!"));
            } else {
                return redirect('poa')->with('success', __("POA Agreement created but file not stored!"));
            }
        }

        return redirect('poa/create')->with('error', __("There has been an error!"));
    }

    public function edit(int $id)
    {
        $poaAgreement = PoaAgreement::find($id);

        if ($poaAgreement) {
            return view('app.poa.edit', ['poaAgreement' => $poaAgreement]);
        }

        return redirect('poa')->with('error', __("Not found!"));
    }

    public function update(Request $request, int $id)
    {
        $poaAgreement = PoaAgreement::find($id);

        if ($poaAgreement) {
            $data = $request->all();

            $validator = Validator::make($data, [
                'title' =>['required', 'string'],
                'text' => ['nullable', 'string'],
                'file' => ['nullable']
            ]);

            $validator->validate();

            $fileName  = false;
            if ($poaAgreement->update($data)) {
                $file = $request->file('file', false);

                if ($file instanceof UploadedFile) {
                    $pathInfos = pathinfo($file->getClientOriginalName());

                    if (!empty($pathInfos['extension'])) {
                        $fileName  = (empty($pathInfos['filename']) ? time() : $pathInfos['filename']) . '_' . $id . '_' . '.' . $pathInfos['extension'];

                        $storeFile = $file->storeAs(PoaAgreement::$storageFolderName, $fileName, PoaAgreement::$fileSystem);

                        if ($storeFile) {
                            PoaAgreement::find($id)->update(['file' => $fileName]);
                        }
                    }
                }

                if ($fileName) {
                    return redirect('poa')->with('success', __("POA Agreement updated!"));
                } else {
                    return redirect('poa')->with('success', __("POA Agreement updated but file not stored!"));
                }
            } else {
                return redirect('poa')->with('error', __("There has been an error!"));
            }
        }

        return redirect('poa')->with('error', __("Not found!"));
    }

    public function destroy(int $id)
    {
        $poaAgreement = PoaAgreement::find($id);

        if ($poaAgreement) {
            $isRemoved = $poaAgreement->update(['is_removed' => BaseModel::$removed]);

            if ($isRemoved) {
                return redirect('poa')->with('success', __("POA Agreement deleted!"));
            } else {
                return redirect('poa')->with('error', __("There has been an error!"));
            }
        }

        return redirect('poa')->with('error', __("Not found!"));
    }

    public function download(int $id)
    {
        $poaAgreement = PoaAgreement::find($id);

        if (!empty($poaAgreement)) {
            $fileUrl = $poaAgreement->getFileUrl();

            if (!empty($fileUrl)) {
                return response()->download($fileUrl);
            }
        }

        return redirect('poa')->with('error', 'File not found!');
    }
}
