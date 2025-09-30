<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Host;
use Illuminate\Support\Facades\DB;

class TourController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Admin/Tours/Index', [
            'tours' => Tour::latest()->paginate(10), // Берем по 10 на страницу
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Tours/Form', [
            'hosts' => Host::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Валидация
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_minor' => 'required|integer|min:0',
            'max_seats' => 'required|integer|min:1',
            'host_id' => 'nullable|exists:hosts,id',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'activities' => 'nullable|array',
            'activities.*.title' => 'required_with:activities|string', // Если активность есть, у нее должен быть title
            'activities.*.description' => 'required_with:activities|string',
            'activities.*.image' => 'nullable|image|max:2048',
            'dates' => 'nullable|array',
            'dates.*' => 'required_with:dates|date', // Каждая дата должна быть датой
        ]);
        
        // Используем транзакцию, чтобы все сохранилось или ничего
        $tour = DB::transaction(function () use ($request, $validated) {
            // 2. Создаем основной тур
            $tour = Tour::create($validated);
            
            // 3. Сохраняем Галерею
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('tours/gallery', 'public');
                    $tour->images()->create(['url' => $path]);
                }
            }
            
            // 4. Сохраняем Активности
            if ($request->input('activities')) {
                foreach ($request->input('activities') as $index => $activityData) {
                    $path = null;
                    if ($request->hasFile("activities.{$index}.image")) {
                        $path = $request->file("activities.{$index}.image")->store('tours/activities', 'public');
                    }
                    $tour->activities()->create([
                        'title' => $activityData['title'],
                        'description' => $activityData['description'],
                        'image_url' => $path,
                    ]);
                }
            }
            
            // 5. Сохраняем Даты
            if (!empty($validated['dates'])) {
                foreach ($validated['dates'] as $date) {
                    if($date) $tour->dates()->create(['date' => $date]);
                }
            }
            
            return $tour;
        });

        return to_route('admin.tours.index')->with('success', 'Тур успешно создан!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tour->load(['host', 'images', 'activities', 'dates'])->findOrFail($id);

        return Inertia::render('Tours/Show', [
            'tour' => $tour,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
