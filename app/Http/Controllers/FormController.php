<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFormRequest;
use App\Http\Requests\UpdateFormRequest;
use App\Models\Form;
use App\Exports\XlsFormExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Illuminate\Support\Str;

class FormController extends Controller
{
    public function index()
    {
        return Inertia::render('forms/Index', [
            'forms' => auth()->user()->forms()->latest()->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('forms/Create');
    }

    public function store(StoreFormRequest $request)
    {
        $validated = $request->validated();

        $form = auth()->user()->forms()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'form_id' => $validated['form_id'] ?? 'survey_' . Str::random(8),
            'definition' => [],
            'settings' => [
                'form_title' => $validated['title'],
                'default_language' => 'French (fr)',
            ],
        ]);

        return redirect()->route('forms.show', $form);
    }

    public function show(Form $form)
    {
        Gate::authorize('view', $form);

        return Inertia::render('forms/Show', [
            'form' => $form,
        ]);
    }

    public function edit(Form $form)
    {
        Gate::authorize('update', $form);

        return Inertia::render('forms/Edit', [
            'form' => $form,
        ]);
    }

    public function update(UpdateFormRequest $request, Form $form)
    {
        $form->update($request->validated());

        return back()->with('success', 'Formulaire mis à jour');
    }

    public function destroy(Form $form)
    {
        Gate::authorize('delete', $form);

        $form->delete();

        return redirect()->route('forms.index')->with('success', 'Formulaire supprimé');
    }

    public function download(Form $form)
    {
        Gate::authorize('view', $form);

        $filename = Str::slug($form->title) . '.xlsx';

        return Excel::download(new XlsFormExport($form), $filename);
    }

    public function uploadAsset(\Illuminate\Http\Request $request, Form $form)
    {
        Gate::authorize('update', $form);

        $request->validate([
            'file' => 'required|file|max:10240',
            'type' => 'required|string|in:image,audio,video,file',
        ]);

        $file = $request->file('file');
        $path = $file->store("forms/{$form->id}", 'public');

        $form->assets()->create([
            'type' => $request->type,
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        return back()->with('success', 'Fichier ajouté');
    }

    public function deleteAsset(Form $form, \App\Models\FormAsset $asset)
    {
        Gate::authorize('update', $form);

        if ($asset->form_id !== $form->id) {
            abort(403);
        }

        \Illuminate\Support\Facades\Storage::disk('public')->delete($asset->path);
        $asset->delete();

        return back()->with('success', 'Fichier supprimé');
    }
}
