<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Repository\ConversationRepository;
use App\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationsController extends Controller
{
    /**
     * @var ConversationRepository
     */
    private $repository;
    /**
     * @var AuthManager
     */
    private $auth;

    public function __construct(ConversationRepository $repository, AuthManager $auth)
    {
        $this->repository = $repository;
        $this->auth = $auth;
    }

    public function index()
    {
        return view('conversations/index', [
            'users' => $this->repository->getConversation($this->auth->user()->id),
        ]);
    }

    public function show(User $user)
    {
        return view('conversations/show', [
            'users' => $this->repository->getConversation($this->auth->user()->id),
            'user' => $user,
            'messages' => $this->repository->getMessagesFrom($this->auth->user()->id, $user->id)->paginate(20)
        ]);
    }

    public function store(User $user, StoreMessageRequest $request)
    {
        $this->repository->createMessage(
            $request->get('content'),
            $this->auth->user()->id,
            $user->id
        );
        return redirect(route('conversations.show', ['id' => $user->id]));
    }
}
