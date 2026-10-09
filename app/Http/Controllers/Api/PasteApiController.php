<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Paste;
use App\Support\PasteData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class PasteApiController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $paste = PasteData::create(PasteData::validate($request), $request->attributes->get('paste_api_token')->id);
        return response()->json(PasteData::response($paste), 201);
    }
    public function show(Paste $paste): JsonResponse
    {
        PasteData::visible($paste);
        abort_if($paste->visibility !== 'public' && $paste->visibility !== 'unlisted', 404);
        return response()->json([...PasteData::response($paste), 'content' => $paste->content])
            ->header('Cache-Control', 'no-store, private')
            ->header('X-Content-Type-Options', 'nosniff');
    }
    public function destroy(Request $request, Paste $paste): JsonResponse
    {
        abort_unless($paste->api_token_id === $request->attributes->get('paste_api_token')->id, 403);
        $paste->delete();
        return response()->json(['deleted'=>true]);
    }
}
