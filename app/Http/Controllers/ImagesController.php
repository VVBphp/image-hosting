<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Storage;

class ImagesController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Image::class, 'image');
    }

    public function index(Request $request)
    {
        $filter['created_between'] = $request->input('filter.created_between') ?? '';
        $filter['user_id'] = $request->input('filter.user_id');

        $images = QueryBuilder::for(Image::when(!auth()->user()->is_admin, function ($query) {
            return $query->whereUserId(auth()->id());
        }))
            ->allowedFilters([
                AllowedFilter::scope('created_between'),
                AllowedFilter::exact('user_id')
            ])
            ->with('user')
            ->latest()
            ->paginate()
            ->withQueryString();

        if (auth()->user()->is_admin) {
            $filter['user_id'] = $request->input('filter.user_id');
            $users = User::get(['login', 'id']);
        } else
            $users = [auth()->user()];

        return Inertia::render('Gallery/Listing', compact('images', 'users', 'filter'));
    }

    public function create()
    {
        return Inertia::render('Gallery/Upload');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:png,jpg,jpeg,gif'
        ]);
        $image = new Image();
        $image->user_id = auth()->id();
        $image->mime = $request->file('image')->getMimeType();
        $image->save();

        $request->file('image')->storeAs('images/' . auth()->id(), $image->code);

        return url('/' . $image->code);
    }

    public function image(Request $request, Image $image)
    {
        if (!auth()->check() && $image->deleted_at !== null) {
            abort(404);
        }
        $fn = 'images/' . $image->user_id . '/' . $image->code;
        $contents = Storage::get($fn);
        $last_modified = Storage::lastModified($fn);

        if ($request->header('If-Modified-Since') == $last_modified) {
            return response('', 304)
                ->header('Cache-control', 'public')
                ->header("Expires", gmdate("D, d M Y H:i:s", time() + 60 * 60 * 24) . " GMT")
                ->header('Last-Modified', gmdate('D, d M Y H:i:s', $last_modified) . ' GMT');
        } else {
            return response($contents)
                ->header('Cache-control', 'public')
                ->header("Expires", gmdate("D, d M Y H:i:s", time() + 60 * 60 * 24) . " GMT")
                ->header('Last-Modified', gmdate('D, d M Y H:i:s', $last_modified) . ' GMT')
                ->header('Content-Length', Storage::size($fn))
                ->header('Content-Type', $image->mime);
        }
    }

    public function show(Request $request, Image $image)
    {
        if (!auth()->check()) {
            return $this->image($request, $image);
        }
        $image->load('user');

        return Inertia::render('Gallery/Show', compact('image'));
    }

    public function destroy(Image $image)
    {
        $image->delete();
        return redirect()->route('images.index');
    }

    public function restore(Image $image)
    {
        $image->restore();
        return redirect()->back();
    }
}
