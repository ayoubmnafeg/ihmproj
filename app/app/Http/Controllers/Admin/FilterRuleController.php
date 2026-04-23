<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FilterRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FilterRuleController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(FilterRule::all());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'rule'                 => 'required|string|max:255',
            'for_label_moderation' => 'boolean',
            'is_enabled'           => 'boolean',
        ]);

        return response()->json(FilterRule::create($data), 201);
    }

    public function update(Request $request, FilterRule $filterRule): JsonResponse
    {
        $data = $request->validate([
            'rule'                 => 'sometimes|string|max:255',
            'for_label_moderation' => 'boolean',
            'is_enabled'           => 'boolean',
        ]);

        $filterRule->update($data);

        return response()->json($filterRule);
    }

    public function destroy(FilterRule $filterRule): JsonResponse
    {
        $filterRule->delete();

        return response()->json(['message' => 'Filter rule deleted.']);
    }
}
