<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuItemRequest;
use App\Http\Requests\UpdateMenuItemRequest;
use App\Models\MenuItem;
use App\Services\CloudinaryMediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class MenuController extends Controller
{
    public function __construct(private CloudinaryMediaService $media) {}

    public function index(Request $request): View
    {
        $search = (string) $request->query('search', '');
        $category = (string) $request->query('category', '');

        $items = MenuItem::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->latest()
            ->get();

        return view('admin-menu', [
            'items' => $items,
            'search' => $search,
            'activeCategory' => $category,
            'categories' => ['Starters', 'Mains', 'Desserts', 'Drinks', 'Sides'],
            'statuses' => ['Available', 'Sold Out', 'Hidden'],
        ]);
    }

    public function store(StoreMenuItemRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['image']);

        if ($request->hasFile('image')) {
            try {
                $upload = $this->media->upload($request->file('image'), 'menu');
                $data['image_url'] = $upload['url'];
                $data['image_public_id'] = $upload['public_id'];
            } catch (RuntimeException|Throwable $exception) {
                report($exception);

                return back()
                    ->withInput($request->except('image'))
                    ->withErrors(['image' => 'Could not upload the photo. Please try again with a JPG or PNG under 5MB.']);
            }
        }

        MenuItem::create($data);

        return redirect()->route('admin.menu.index')->with('status', 'Food item added successfully.');
    }

    public function update(UpdateMenuItemRequest $request, MenuItem $menu): RedirectResponse
    {
        $data = $request->safe()->except(['image']);

        if ($request->hasFile('image')) {
            try {
                $this->media->delete($menu->image_public_id);
                $upload = $this->media->upload($request->file('image'), 'menu');
                $data['image_url'] = $upload['url'];
                $data['image_public_id'] = $upload['public_id'];
            } catch (RuntimeException|Throwable $exception) {
                report($exception);

                return back()
                    ->withInput($request->except('image'))
                    ->withErrors(['image' => 'Could not upload the photo. Please try again with a JPG or PNG under 5MB.']);
            }
        }

        $menu->update($data);

        return redirect()->route('admin.menu.index')->with('status', 'Food item updated successfully.');
    }

    public function destroy(MenuItem $menu): RedirectResponse
    {
        $this->media->delete($menu->image_public_id);
        $menu->delete();

        return redirect()->route('admin.menu.index')->with('status', 'Food item deleted successfully.');
    }
}
