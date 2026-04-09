<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PostController extends Controller
{
    public function index(): JsonResponse
    {
        $posts = Post::with('user')
            ->active()
            ->paginate(20);

        return response()->json($posts);
    }

    public function create(): string
    {
        return 'posts.create';
    }

    public function store(PostRequest $request): JsonResponse
    {
        $post = Post::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);

        return response()->json($post, Response::HTTP_CREATED);
    }

    public function show(Post $post): JsonResponse
    {
        abort_unless(
            Post::active()->whereKey($post->id)->exists(),
            Response::HTTP_NOT_FOUND
        );

        return response()->json($post->load('user'));
    }

    public function edit(Post $post): string
    {
        $this->authorize('update', $post);

        return 'posts.edit';
    }

    public function update(PostRequest $request, Post $post): JsonResponse
    {
        $this->authorize('update', $post);

        $post->update($request->validated());

        return response()->json($post);
    }

    public function destroy(Post $post): JsonResponse
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
