<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WeatherController extends Controller
{
    public function __construct(private WeatherService $weather) {}

    public function index()
    {
        try {
            $data = $this->weather->getByCity('Tallinn');
        } catch (\Exception $e) {
            $data  = null;
            $error = $e->getMessage();
        }

        return Inertia::render('Weather/Index', [
            'weather'     => $data ?? null,
            'error'       => $error ?? null,
            'defaultCity' => 'Tallinn',
        ]);
    }

    public function search(Request $request)
    {
        $request->validate(['city' => 'required|string|max:100']);

        try {
            $data = $this->weather->getByCity($request->city);
            return response()->json(['weather' => $data]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
