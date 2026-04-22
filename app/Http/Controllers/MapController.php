<?php

namespace App\Http\Controllers;

use App\Models\Marker;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MapController extends Controller
{
    public function index()
    {
        return Inertia::render('Map/Index', [
            'markers' => Marker::orderBy('added', 'desc')->get(),
        ]);
    }

    public function markers()
    {
        return response()->json(Marker::orderBy('added', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'latitude'    => 'required|numeric|between:-90,90',
            'longitude'   => 'required|numeric|between:-180,180',
            'description' => 'nullable|string|max:1000',
        ]);

        $marker = Marker::create([
            ...$validated,
            'added' => now(),
        ]);

        return response()->json($marker, 201);
    }

    public function update(Request $request, Marker $marker)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $marker->update([...$validated, 'edited' => now()]);

        return response()->json($marker);
    }

    public function destroy(Marker $marker)
    {
        $marker->delete();
        return response()->json(['message' => 'Marker kustutatud.']);
    }
}
