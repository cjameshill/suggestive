<?php

namespace App\Http\Controllers\Api;

use App\ApiObjects\Topic as ApiTopic;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Topic;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopicsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->get('status')) {
            return $this->indexByStatus($request->get('status'));
        }

        return Topic::active()->get()->map(function ($topic) {
            return new ApiTopic($topic);
        });
    }

    private function unflaggedIndex()
    {
        return Topic::unflagged()->active()->get()->map(function ($topic) {
            return new ApiTopic($topic);
        });
    }

    private function validateStatus($status)
    {
        if (! Topic::isValidStatus($status)) {
            throw new Exception('Invalid status.');
        }
    }

    private function indexByStatus($status)
    {
        $this->validateStatus($status);

        return Topic::where('status', $status)->active()->get()->map(function ($topic) {
            return new ApiTopic($topic);
        });
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => ''
        ]);

        $topic = new Topic;
        $topic->title = $request->get('title');
        $topic->description = $request->get('description');
        $topic->status = Auth::user()->isAdmin() ? Topic::FLAG_ACCEPTED : Topic::FLAG_SUGGESTED;

        Auth::user()->topics()->save($topic);

        return response()->json(new ApiTopic($topic), 201);
    }

    public function show($id)
    {
        return new ApiTopic(Topic::findOrFail($id));
    }

    public function patch($id, Request $request)
    {
        $topic = Topic::findOrFail($id);
        $this->authorize('update-topic', $topic);

        $topic->patch($request->all());

        return response('', 200);
    }
}
