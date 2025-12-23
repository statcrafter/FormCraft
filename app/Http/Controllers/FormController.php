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
}
