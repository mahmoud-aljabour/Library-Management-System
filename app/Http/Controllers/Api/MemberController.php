<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;

class MemberController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Member::class, 'member');
    }

    public function index()
    {
        $members = Member::withCount([
            'borrowings as active_borrowings_count' => fn ($q) => $q->active(),
        ])->orderBy('name')->paginate(15);

        return MemberResource::collection($members);
    }

    public function store(StoreMemberRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        $member = Member::create($data);

        return (new MemberResource($member))->response()->setStatusCode(201);
    }

    public function show(Member $member)
    {
        $member->loadCount([
            'borrowings as active_borrowings_count' => fn ($q) => $q->active(),
        ]);

        return new MemberResource($member);
    }

    public function update(UpdateMemberRequest $request, Member $member)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $member->update($data);

        return new MemberResource($member);
    }

    public function destroy(Member $member)
    {
        if ($member->currentBorrowings()->exists()) {
            return response()->json([
                'message' => 'Cannot delete a member with active borrowings.',
            ], 422);
        }

        $member->delete();

        return response()->json(['message' => 'Member deleted successfully.']);
    }
}
