<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $users
    ) {
    }

    public function index(): View
    {
        return view('users.index', [
            'users' => $this->users->list(10),
        ]);
    }

    public function create(): View
    {
        return view('users.create', [
            'roles' => $this->users->roles(),
        ]);
    }

    public function edit(string $id): View
    {
        $user = $this->users->detail($id);
        abort_if($user === null, 404);

        return view('users.edit', [
            'user' => $user,
            'roles' => $this->users->roles(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'is_active' => ['nullable', 'boolean'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
        ]);

        $this->users->create($data, $request);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $user = $this->users->detail($id);
        abort_if($user === null, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($id, 'id')],
            'password' => ['nullable', 'string', 'min:8'],
            'is_active' => ['nullable', 'boolean'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
        ]);

        $this->users->update($user, $data, $request);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }
}
