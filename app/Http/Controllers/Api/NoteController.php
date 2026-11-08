<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NoteController extends Controller
{
    /**
     * Display a listing of the user's notes.
     */
    public function index(Request $request): JsonResponse
    {
        $query = $request->user()->notes()->with('category');

        // Filter by date
        if ($request->has('date')) {
            $query->whereDate('note_date', $request->date);
        }

        // Filter by date range (week view)
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('note_date', [$request->start_date, $request->end_date]);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter favorites
        if ($request->has('is_favorite')) {
            $query->where('is_favorite', $request->boolean('is_favorite'));
        }

        // Search by title or content
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $notes = $query->orderBy('note_date', 'desc')
                       ->orderBy('updated_at', 'desc')
                       ->get();

        return response()->json($notes);
    }

    /**
     * Store a newly created note.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'note_date' => 'nullable|date',
            'is_favorite' => 'nullable|boolean',
        ]);

        $note = $request->user()->notes()->create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category_id' => $validated['category_id'] ?? null,
            'note_date' => $validated['note_date'] ?? now()->toDateString(),
            'is_favorite' => $validated['is_favorite'] ?? false,
        ]);

        $note->load('category');

        return response()->json($note, 201);
    }

    /**
     * Display the specified note.
     */
    public function show(Request $request, Note $note): JsonResponse
    {
        // Ensure the note belongs to the authenticated user
        if ($note->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $note->load('category');

        return response()->json($note);
    }

    /**
     * Update the specified note.
     */
    public function update(Request $request, Note $note): JsonResponse
    {
        // Ensure the note belongs to the authenticated user
        if ($note->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'category_id' => 'nullable|exists:categories,id',
            'note_date' => 'nullable|date',
            'is_favorite' => 'nullable|boolean',
        ]);

        $note->update($validated);
        $note->load('category');

        return response()->json($note);
    }

    /**
     * Remove the specified note.
     */
    public function destroy(Request $request, Note $note): JsonResponse
    {
        // Ensure the note belongs to the authenticated user
        if ($note->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $note->delete();

        return response()->json(['message' => 'Note deleted successfully']);
    }
}
