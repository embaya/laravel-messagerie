<?php
/**
 * Created by PhpStorm.
 * User: Majed
 * Date: 13/01/2018
 * Time: 22:30
 */


namespace App\Repository;

use App\User;
use App\Message;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class ConversationRepository
{
    /**
     * @var User
     */
    private $user;
    /**
     * @var Message
     */
    private $message;

    public function __construct(User $user, Message $message)
    {
        $this->user = $user;
        $this->message = $message;
    }

    public function getConversation(int $userId)
    {
        return $this->user->newQuery()
            ->select('name', 'id')
            ->where('id', '!=', $userId)
            ->get();
    }

    public function createMessage(string $content, int $from, int $to)
    {
        return $this->message->newQuery()->create([
            'content' => $content,
            'from_id' => $from,
            'to_id' => $to,
            'creted_at' => Carbon::now()
        ]);
    }

    public function getMessagesFrom(int $from, int $to) : Builder
    {
        return $this->message->newQuery()
            ->whereRaw("((from_id = $from AND to_id = $to) OR (from_id = $to AND to_id = $from))")
            ->orderBy('created_at', 'DESC')
            ->with([
                'from' => function ($query){ return $query->select('name', 'id');}
            ]);
    }
}