<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateAdminAction;
use App\Actions\ForceChangePasswordAction;
use App\Helpers\SortHelper;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\Auth\RegisterAdminRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function index(SortHelper $sorter): Response
    {
        $users = User::query()->role("admin")->with("school");

        $query = $sorter->sort($users, ["id", "email", "updated_at", "created_at"], ["firstname"]);
        $query = $this->sortByName($query, $sorter);
        $query = $sorter->search($query, "firstname");

        return Inertia::render("Admin/AdminsPanel", [
            "users" => UserResource::collection($query->paginate()),
        ]);
    }

    public function store(RegisterAdminRequest $request, ForceChangePasswordAction $forceChangePasswordAction, CreateAdminAction $createAdminAction): RedirectResponse
    {
        $user = $createAdminAction->execute($request->validated());
        $forceChangePasswordAction->execute($user);

        return redirect()
            ->route("admin.admins.index")
            ->with("status", "Administrator został utworzony pomyślnie.");
    }

    public function update(AdminRequest $request, User $user): RedirectResponse
    {
        $this->authorize("update", $user);
        $data = $request->validated();

        if (!$request->has("password")) {
            unset($data["password"]);
        }

        $user->update($data);

        if ($request->has("password")) {
            $user->password = Hash::make($request->validated("password"));
            $user->save();
        }

        return redirect()
            ->route("admin.admins.index")
            ->with("status", "Administrator zaktualizowany pomyślnie.");
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize("delete", $user);
        $user->delete();

        return redirect()->back();
    }

    private function sortByName(Builder $query, SortHelper $sorter): Builder
    {
        [$field, $order] = $sorter->getSortParameters();

        if ($field === "firstname") {
            return $query->orderBy("firstname", $order)
                ->orderBy("surname", $order);
        }

        return $query;
    }
}
