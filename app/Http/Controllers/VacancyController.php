<?php

namespace App\Http\Controllers;

use App\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VacancyController extends Controller
{
    public function index(Request $request)
    {
        $vacancies = Vacancy::orderBy('created_at', 'desc')->get();

        return response()->json($vacancies);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'hours' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'images' => ['nullable', 'array', 'max:3'],
            'images.*' => ['file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $urls = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('vacancies', 'public');
                $urls[] = Storage::url($path);
            }
        }

        $vacancy = Vacancy::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'hours' => $validated['hours'] ?? null,
            'location' => $validated['location'] ?? null,
            'employment_type' => $validated['employment_type'] ?? null,
            'description' => $validated['description'] ?? null,
            'images' => $urls,
            'created_by' => Auth::id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['id' => $vacancy->id, 'vacancy' => $vacancy], 201);
        }

        return redirect()->back()->with('status', 'Vacancy created');
    }
}
