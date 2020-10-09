<?php

namespace App\Http\Controllers;

use App\Role;
use App\BaseModel;
use App\TranslateModelDocument;
use App\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Http\UploadedFile;

class TranslateModelDocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:translate_model_document_access'])->only('index');
        $this->middleware(['permission:translate_model_document_create'])->only(['create','store']);
        $this->middleware(['permission:translate_model_document_edit'])->only(['edit','update']);
        $this->middleware(['permission:translate_model_document_delete'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $translateModelDocument = new TranslateModelDocument();
        $isFiltered             = false;

        if (count($request->all()) > 0) {
            $isFiltered = (!empty(array_filter($request->all())));
        }

        $translateModelDocuments = $translateModelDocument::query();

        if ($isFiltered) {
            if ($request->get('s', false)) {
                $s = $request->get('s');

                $translateModelDocuments->where(function($query) use($s) {
                    $query->where('title','LIKE',"%$s%")
                          ->orWhere('text','LIKE',"%$s%");
                });
            }
        }

        $translateModelDocuments = $translateModelDocuments->where('is_removed', BaseModel::$notRemoved)->paginate(20);

        return view('app.translate_model_document.list', ['translateModelDocuments' => $translateModelDocuments, 'term' => $request, 'isFiltered' => $isFiltered]);
    }

    public function create()
    {
        $assignTo    = [];
        $clientRoles = Role::whereRaw("lower(name) = 'client'")->first();
        if (!empty($clientRoles)) {
            $assignTo = $clientRoles->users;
        }

        return view('app.translate_model_document.create', ['assignTo' => $assignTo]);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'title'     => ['required', 'string'],
            'text'      => ['nullable', 'string'],
            'file'      => ['nullable'],
            'client_id' => ['required', 'integer', 'exists:' . Client::getTableName() . ',id'],
        ]);

        $validator->validate();

        $translateModelDocument = TranslateModelDocument::create([
            'title'     => $data['title'],
            'text'      => $data['text'],
            'client_id' => $data['client_id']
        ]);

        $storeFile = false;
        if ($translateModelDocument) {
            $id   = $translateModelDocument->id;
            $file = $request->file('file', false);

            if (!empty($id) && $file instanceof UploadedFile) {
                $pathInfos = pathinfo($file->getClientOriginalName());

                if (!empty($pathInfos['extension'])) {
                    $fileName  = (empty($pathInfos['filename']) ? time() : $pathInfos['filename']) . '_' . $id . '_' . '.' . $pathInfos['extension'];

                    $storeFile = $file->storeAs(TranslateModelDocument::$storageFolderName, $fileName, TranslateModelDocument::$fileSystem);

                    if ($storeFile) {
                        TranslateModelDocument::find($id)->update(['file' => $fileName]);
                    }
                }
            }

            if ($storeFile) {
                return redirect('translate_model_document')->with('success', __("Translate Model Document created!"));
            } else {
                return redirect('translate_model_document')->with('success', __("Translate Model Document created but file not stored!"));
            }
        }

        return redirect('translate_model_document/create')->with('error', __("There has been an error!"));
    }

    public function edit(int $id)
    {
        $translateModelDocument = TranslateModelDocument::find($id);

        $assignTo    = [];
        $clientRoles = Role::whereRaw("lower(name) = 'client'")->first();
        if (!empty($clientRoles)) {
            $assignTo = $clientRoles->users;
        }

        if ($translateModelDocument) {
            return view('app.translate_model_document.edit', ['translateModelDocument' => $translateModelDocument, 'assignTo' => $assignTo]);
        }

        return redirect('translate_model_document')->with('error', __("Not found!"));
    }

    public function update(Request $request, int $id)
    {
        $translateModelDocument = TranslateModelDocument::find($id);

        if ($translateModelDocument) {
            $data = $request->all();

            $validator = Validator::make($data, [
                'title'     => ['required', 'string'],
                'text'      => ['nullable', 'string'],
                'file'      => ['nullable'],
                'client_id' => ['required', 'integer', 'exists:' . Client::getTableName() . ',id'],
            ]);

            $validator->validate();

            $fileName  = false;
            if ($translateModelDocument->update($data)) {
                $file = $request->file('file', false);

                if ($file instanceof UploadedFile) {
                    $pathInfos = pathinfo($file->getClientOriginalName());

                    if (!empty($pathInfos['extension'])) {
                        $fileName  = (empty($pathInfos['filename']) ? time() : $pathInfos['filename']) . '_' . $id . '_' . '.' . $pathInfos['extension'];

                        $storeFile = $file->storeAs(TranslateModelDocument::$storageFolderName, $fileName, TranslateModelDocument::$fileSystem);

                        if ($storeFile) {
                            TranslateModelDocument::find($id)->update(['file' => $fileName]);
                        }
                    }
                }

                if ($fileName) {
                    return redirect('translate_model_document')->with('success', __("Translate Model Document updated!"));
                } else {
                    return redirect('translate_model_document')->with('success', __("Translate Model Document updated but file not stored!"));
                }
            } else {
                return redirect('translate_model_document')->with('error', __("There has been an error!"));
            }

            return redirect('translate_model_document')->with('error', __("There has been an error!"));
        }

        return redirect('translate_model_document')->with('error', __("Not found!"));
    }

    public function destroy(int $id)
    {
        $translateModelDocument = TranslateModelDocument::find($id);

        if ($translateModelDocument) {
            $isRemoved = $translateModelDocument->update(['is_removed' => BaseModel::$removed]);

            if ($isRemoved) {
                return redirect('translate_model_document')->with('success', __("Translate Model Document deleted!"));
            } else {
                return redirect('translate_model_document')->with('error', __("There has been an error!"));
            }
        }

        return redirect('translate_model_document')->with('error', __("Not found!"));
    }

    public function email($id, Request $request)
    {
        $emailIds = $request->get('emails');

        return redirect('translate_model_document')->with('success', __("Emails sent successfully!"));
    }

    public function download(int $id)
    {
        $translateModelDocument = TranslateModelDocument::find($id);

        if (!empty($translateModelDocument)) {
            $fileUrl = $translateModelDocument->getFileUrl();

            if (!empty($fileUrl)) {
                return response()->download($fileUrl);
            }
        }

        return redirect('translate_model_document')->with('error', 'File not found!');
    }
}
