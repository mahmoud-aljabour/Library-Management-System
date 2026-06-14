<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePublisherRequest;
use App\Http\Requests\UpdatePublisherRequest;
use App\Models\Publisher;

class PublisherController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Publisher::class, 'publisher');
    }

    public function index()
    {
        $publishers = Publisher::withCount('books')->orderBy('name')->paginate(10);

        return view('publishers.index', compact('publishers'));
    }

    public function create()
    {
        return view('publishers.create');
    }

    public function store(StorePublisherRequest $request)
    {
        Publisher::create($request->validated());

        return redirect()->route('publishers.index')->with('success', 'Publisher created successfully.');
    }

    public function show(Publisher $publisher)
    {
        $publisher->load(['books' => fn ($q) => $q->latest()->limit(10)]);

        return view('publishers.show', compact('publisher'));
    }

    public function edit(Publisher $publisher)
    {
        return view('publishers.edit', compact('publisher'));
    }

    public function update(UpdatePublisherRequest $request, Publisher $publisher)
    {
        $publisher->update($request->validated());

        return redirect()->route('publishers.index')->with('success', 'Publisher updated successfully.');
    }

    public function destroy(Publisher $publisher)
    {
        if ($publisher->books()->exists()) {
            return redirect()->route('publishers.index')
                ->with('error', 'Cannot delete a publisher with associated books.');
        }

        $publisher->delete();

        return redirect()->route('publishers.index')->with('success', 'Publisher deleted successfully.');
    }
}
