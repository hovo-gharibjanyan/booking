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
        // 1. ВАЛИДАЦИЯ ВСЕХ ВХОДЯЩИХ ДАННЫХ
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_minor' => 'required|integer|min:0',
            'max_seats' => 'required|integer|min:1',
            'host_id' => 'nullable|exists:hosts,id',
            'images' => 'nullable|array',
            'images.*' => 'sometimes|image|max:2048', // Каждое изображение в галерее
            'activities' => 'nullable|array',
            'activities.*.title' => 'required_with:activities|string',
            'activities.*.description' => 'required_with:activities|string',
            'activities.*.image' => 'nullable|image|max:2048',
            'dates' => 'nullable|array',
            'dates.*' => 'required_with:dates|date',
        ]);
        
        // Используем транзакцию, чтобы все операции выполнились успешно, или ни одной
        DB::transaction(function () use ($request, $validated) {
            
            // 2. СОЗДАЕМ ОСНОВНОЙ ТУР
            // Мы передаем только те поля, которые относятся к самой таблице tours
            $tour = Tour::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'price_minor' => $validated['price_minor'],
                'max_seats' => $validated['max_seats'],
                'host_id' => $validated['host_id'] ?? null,
            ]);
            
            // 3. СОХРАНЯЕМ ГАЛЕРЕЮ ИЗОБРАЖЕНИЙ
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    // Сохраняем файл и получаем путь к нему
                    $path = $file->store('tours/gallery', 'public');
                    // Создаем связанную запись в таблице tour_images
                    $tour->images()->create(['url' => $path]);
                }
            }
            
            // 4. СОХРАНЯЕМ ПРОГРАММУ ТУРА ("АКТИВНОСТИ")
            if ($request->input('activities')) {
                foreach ($request->input('activities') as $index => $activityData) {
                    // Проверяем, был ли загружен файл для этой активности
                    $activityImagePath = null;
                    if ($request->hasFile("activities.{$index}.image")) {
                        $activityImagePath = $request->file("activities.{$index}.image")->store('tours/activities', 'public');
                    }
                    
                    // Создаем связанную запись в таблице tour_activities
                    $tour->activities()->create([
                        'title' => $activityData['title'],
                        'description' => $activityData['description'],
                        'image_url' => $activityImagePath,
                    ]);
                }
            }
            
            // 5. СОХРАНЯЕМ ДОСТУПНЫЕ ДАТЫ
            if (!empty($validated['dates'])) {
                foreach ($validated['dates'] as $date) {
                    // Проверяем, что дата не пустая, на случай если пользователь добавил лишнее поле
                    if($date) {
                        $tour->dates()->create(['date' => $date]);
                    }
                }
            }
        });
    
        // 6. ВОЗВРАЩАЕМ АДМИНА НА СПИСОК ТУРОВ
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
        $tour = Tour::findOrFail($id);
        $tour->load(['host', 'images', 'activities', 'dates']);

        return Inertia::render('Admin/Tours/Form', [
            'tour' => $tour,
            'hosts' => Host::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_minor' => 'required|integer|min:0',
            'max_seats' => 'required|integer|min:1',
            'host_id' => 'nullable|exists:hosts,id',
        ]);

        $tour = Tour::findOrFail($id);
        if ($tour->bookings()->where('status', '!=', 'cancelled')->count() > 0) {
            return back()->with('error', 'Нельзя редактировать тур, у которого есть брони');
        }
        $tour->update($validated);
        return to_route('admin.tours.index')->with('success', 'Тур успешно обновлен!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tour = Tour::findOrFail($id);
        if ($tour->bookings()->where('status', '!=', 'cancelled')->exists()) {
        
            return back()->with('error', 'Нельзя удалить тур, на который уже есть активные бронирования!');
        }
        $tour->delete();
        return back()->with('success', 'Тур успешно удален!');
    }

    
}
