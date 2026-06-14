<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
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
        ])->orderBy('name')->paginate(10);

        return view('members.index', compact('members'));
    }

    public function create()
    {
        return view('members.create');
    }

    public function store(StoreMemberRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        Member::create($data);

        return redirect()->route('members.index')->with('success', 'Member created successfully.');
    }

    public function show(Member $member)
    {
        $member->load(['borrowings.book', 'reviews']);

        return view('members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        return view('members.edit', compact('member'));
    }

    public function update(UpdateMemberRequest $request, Member $member)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $member->update($data);

        return redirect()->route('members.index')->with('success', 'Member updated successfully.');
    }

    public function destroy(Member $member)
    {
        if ($member->currentBorrowings()->exists()) {
            return redirect()->route('members.index')
                ->with('error', 'Cannot delete a member with active borrowings.');
        }

        $member->delete();

        return redirect()->route('members.index')->with('success', 'Member deleted successfully.');
    }

    public function toggleStatus(Member $member)
    {
        $this->authorize('update', $member);

        $member->update(['is_active' => ! $member->is_active]);

        return redirect()->back()->with('success', 'Member status updated successfully.');
    }
}
