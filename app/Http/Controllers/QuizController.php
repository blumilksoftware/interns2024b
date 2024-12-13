<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\AssignToQuizAction;
use App\Actions\CreateUserQuizAction;
use App\Actions\LockQuizAction;
use App\Actions\SetQuizLocalAction;
use App\Actions\SetQuizOnlineAction;
use App\Actions\UnlockQuizAction;
use App\Helpers\SortHelper;
use App\Http\Requests\QuizRequest;
use App\Http\Requests\UpdateQuizRequest;
use App\Http\Resources\QuizResource;
use App\Models\Quiz;
use App\Services\QuizCloneService;
use App\Services\QuizUpdateService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

use function collect;
use function redirect;

class QuizController extends Controller
{
    public function index(Request $request, SortHelper $sorter): Response
    {
        $query = $sorter->sort(Quiz::query()->with("questions.answers"), ["id", "title", "updated_at", "created_at"], []);
        $query = $this->filterArchivedQuizzes($query, $request);
        $query = $sorter->search($query, "title");
        $quizzes = $sorter->paginate($query);

        return Inertia::render("Admin/Quizzes", ["quizzes" => QuizResource::collection($quizzes)]);
    }

    public function show(Quiz $quiz): Response
    {
        return Inertia::render(
            "Admin/QuizDemo",
            ["quiz" => $quiz->load("questions.answers")],
        );
    }

    public function store(QuizRequest $request): RedirectResponse
    {
        Quiz::query()->create($request->validated());

        return redirect()->back();
    }

    public function update(QuizUpdateService $service, UpdateQuizRequest $request, Quiz $quiz): RedirectResponse
    {
        $service->update($quiz, $request->validated());

        return redirect()->back();
    }

    public function destroy(Quiz $quiz): RedirectResponse
    {
        $quiz->delete();

        return redirect()->back();
    }

    public function clone(QuizCloneService $service, Quiz $quiz): RedirectResponse
    {
        $service->cloneQuiz($quiz);

        return redirect()
            ->back()
            ->with("status", "Test został skopiowany");
    }

    public function lock(LockQuizAction $action, Quiz $quiz): RedirectResponse
    {
        $action->execute($quiz);

        return redirect()
            ->back()
            ->with("status", "Test oznaczony jako gotowy do publikacji");
    }

    public function unlock(UnlockQuizAction $action, Quiz $quiz): RedirectResponse
    {
        $action->execute($quiz);

        return redirect()
            ->back()
            ->with("status", "Publikacja testu została wycofana");
    }

    public function createUserQuiz(CreateUserQuizAction $action, Request $request, Quiz $quiz): RedirectResponse
    {
        $userQuiz = $action->execute($quiz, $request->user());

        return redirect("/quizzes/{$userQuiz->id}/");
    }

    public function assign(AssignToQuizAction $action, Request $request, Quiz $quiz): RedirectResponse
    {
        $action->execute($quiz, collect([$request->user()->id]));

        return redirect()
            ->back()
            ->with("status", "Przypisano do testu");
    }

    public function makeLocal(SetQuizLocalAction $action, Quiz $quiz): RedirectResponse
    {
        $action->execute($quiz);

        return redirect()
            ->back()
            ->with("status", "Zmiana quizu na stacjonarny");
    }

    public function makeOnline(SetQuizOnlineAction $action, Quiz $quiz): RedirectResponse
    {
        $action->execute($quiz);

        return redirect()
            ->back()
            ->with("status", "Zmiana quizu na online");
    }

    private function filterArchivedQuizzes(Builder $query, Request $request): Builder
    {
        $showArchived = $request->query("archived", "false") === "true";

        if (!$showArchived) {
            return $query->orWhere(fn(Builder $query) => $query->whereNull("locked_at")->orWhere("scheduled_at", ">", Carbon::now()));
        }

        return $query;
    }
}
