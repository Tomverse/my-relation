<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Psr\Http\Message\ServerRequestInterface;

class UserController extends SearchableController
{
    const array ROLES = ['ADMIN', 'USER'];

    #[\Override]
    function getQuery(): Builder
    {
        return User::orderBy('email');
    }

    #[\Override]
    function getFilterOptions(): array
    {
        return [
            'term' => [
                'email' => static fn (Builder $query, string $word)
                    => $query->where('email', 'LIKE', "%{$word}%"),
                'name' => static fn (Builder $query, string $word)
                    => $query->where('name', 'LIKE', "%{$word}%"),
                'role' => static fn (Builder $query, string $word)
                    => $query->where('role', 'LIKE', "%{$word}%"),
            ],
        ];
    }

  
    #[\Override]
    function find(string $code): Model
    {
        return $this->getQuery()->where('email', $code)->firstOrFail();
    }

    function list(ServerRequestInterface $request): View
    {
        Gate::authorize('list', User::class);

        $criteria = $this->prepareCriteria($request->getQueryParams());
        $query = $this->search($criteria);

        return view('users.list', [
            'title' => 'List',
            'criteria' => $criteria,
            'users' => $query->paginate(static::MAX_ITEMS),
        ]);
    }

    function view(string $user): View
    {
        $user = $this->find($user);
        Gate::authorize('view', $user);

        return view('users.view', [
            'title' => $user->email,
            'user' => $user,
        ]);
    }

    function showCreateForm(): View
    {
        Gate::authorize('create', User::class);

        return view('users.create-form', [
            'title' => 'Create',
            'roles' => static::ROLES,
        ]);
    }

    function create(ServerRequestInterface $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $validator = Validator::make(
            $request->getParsedBody() ?? [],
            [
                'email' => ['required', 'email', 'unique:users,email'],
                'name' => ['required'],
                'password' => ['required'],
                'role' => ['required', 'in:' . \implode(',', static::ROLES)],
            ],
        );

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $data = $validator->validated();

        try {
            
            $user = new User();
            $user->fill($data);
            $user->email = $data['email'];
            $user->role = $data['role'];
            $user->save();

            if (session()->has('bookmarks.users.create')) {
                session()->put(
                    'bookmarks.users.view',
                    session()->get('bookmarks.users.create'),
                );
            }
            session()->forget('bookmarks.users.create');

            return redirect()
                ->route('users.view', [
                    'user' => $user->email,
                ])
                ->with('status', "User {$user->email} was created.");
        } catch (QueryException $excp) {
            return redirect()->back()->withInput()->withErrors([
                'alert' => $excp->errorInfo[2] ?? $excp->getMessage(),
            ]);
        } catch (\Exception $excp) {
            return redirect()->back()->withInput()->withErrors([
                'alert' => $excp->getMessage(),
            ]);
        }
    }

    function showUpdateForm(string $user): View
    {
        $user = $this->find($user);
        Gate::authorize('update', $user);

        return view('users.update-form', [
            'title' => $user->email,
            'user' => $user,
            'roles' => static::ROLES,
        ]);
    }

    function update(
        string $user,
        ServerRequestInterface $request,
    ): RedirectResponse {
        $user = $this->find($user);
        Gate::authorize('update', $user);

        $data = $request->getParsedBody() ?? [];
        $canUpdateRole = Gate::allows('updateRole', $user);

        $validator = Validator::make(
            $data,
            [
                'name' => ['required'],
                'role' => $canUpdateRole ? ['required', 'in:' . \implode(',', static::ROLES)] : [],
            ],
        );

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        
        if (($data['password'] ?? '') === '') {
            unset($data['password']);
        }

        try {
            $user->fill($data);

            
            if ($canUpdateRole) {
                $user->role = $data['role'];
            }

            $user->save();

            if (session()->has('bookmarks.users.update')) {
                session()->put(
                    'bookmarks.users.view',
                    session()->get('bookmarks.users.update'),
                );
            }
            session()->forget('bookmarks.users.update');

            return redirect()
                ->route('users.view', [
                    'user' => $user->email,
                ])
                ->with('status', "User {$user->email} was updated.");
        } catch (QueryException $excp) {
            return redirect()->back()->withInput()->withErrors([
                'alert' => $excp->errorInfo[2] ?? $excp->getMessage(),
            ]);
        } catch (\Exception $excp) {
            return redirect()->back()->withInput()->withErrors([
                'alert' => $excp->getMessage(),
            ]);
        }
    }

    function delete(string $user): RedirectResponse
    {
        $user = $this->find($user);
        Gate::authorize('delete', $user);

        try {
            $user->delete();

            return redirect(
                session()->get('bookmarks.users.delete') ?? route('users.index'),
            )->with('status', "User {$user->email} was deleted.");
        } catch (QueryException $excp) {
           
            return redirect()->back()->withErrors([
                'alert' => $excp->errorInfo[2] ?? $excp->getMessage(),
            ]);
        } catch (\Exception $excp) {
            return redirect()->back()->withErrors([
                'alert' => $excp->getMessage(),
            ]);
        }
    }

    function viewSelf(): View
    {
        $user = Auth::user();
        assert($user instanceof User);

        return view('users.selves.view', [
            'title' => 'Self',
            'user' => $user,
        ]);
    }

    function showUpdateSelfForm(): View
    {
        $user = Auth::user();
        assert($user instanceof User);

        return view('users.selves.update-form', [
            'title' => 'Self',
            'user' => $user,
        ]);
    }

        function updateSelf(ServerRequestInterface $request): RedirectResponse
    {
        $user = Auth::user();
        assert($user instanceof User);

        $data = $request->getParsedBody() ?? [];

        $canUpdateEmail = Gate::allows('updateEmail', $user);

        $validator = Validator::make($data, [
            'name' => ['required'],
            'email' => $canUpdateEmail ? ['required', 'email', "unique:users,email,{$user->id}"] : [],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        if (($data['password'] ?? '') === '') {
            unset($data['password']);
        }

        try {
            $user->fill($data);

            if ($canUpdateEmail) {
                $user->email = $data['email'];
            }

            $user->save();

            if (session()->has('bookmarks.users.selves.update')) {
                session()->put(
                    'bookmarks.users.selves.view',
                    session()->get('bookmarks.users.selves.update'),
                );
            }
            session()->forget('bookmarks.users.selves.update');

            return redirect()
                ->route('users.selves.view')
                ->with('status', 'Your information was updated.');
        } catch (QueryException $excp) {
            return redirect()->back()->withInput()->withErrors([
                'alert' => $excp->errorInfo[2] ?? $excp->getMessage(),
            ]);
        } catch (\Exception $excp) {
            return redirect()->back()->withInput()->withErrors([
                'alert' => $excp->getMessage(),
            ]);
        }
    
        }
        }
