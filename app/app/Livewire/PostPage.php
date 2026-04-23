<?php

namespace App\Livewire;

use App\Models\Content;
use App\Models\Comment;
use App\Models\PollOption;
use App\Models\PollVote;
use App\Models\Publication;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PostPage extends Component
{
    public string $publicationId;
    public string $newComment = '';
    public array $replyDrafts = [];
    public array $openReplyForms = [];
    public ?string $pendingDeleteCommentId = null;

    public function mount(string $publicationId): void
    {
        $this->publicationId = $publicationId;
    }

    public function getPublicationProperty(): Publication
    {
        return Publication::with([
            'author.profile',
            'category',
            'attachments',
            'poll.options',
            'poll.votes' => fn ($voteQuery) => $voteQuery->where('user_id', auth()->id()),
            'reactions' => fn ($reactionQuery) => $reactionQuery->where('user_id', auth()->id()),
            'comments' => fn ($commentQuery) => $commentQuery
                ->with(['author.profile', 'reactions' => fn ($reactionQuery) => $reactionQuery->where('user_id', auth()->id())])
                ->withCount([
                    'reactions as upvotes_count' => fn ($reactionQuery) => $reactionQuery->where('type', 'upvote'),
                    'reactions as downvotes_count' => fn ($reactionQuery) => $reactionQuery->where('type', 'downvote'),
                ])
                ->latest('contents.created_at'),
        ])
            ->withCount([
                'comments',
                'reactions as upvotes_count' => fn ($reactionQuery) => $reactionQuery->where('type', 'upvote'),
                'reactions as downvotes_count' => fn ($reactionQuery) => $reactionQuery->where('type', 'downvote'),
            ])
            ->where('contents.status', 'visible')
            ->where('publications.id', $this->publicationId)
            ->firstOrFail();
    }

    public function votePoll(string $optionId): void
    {
        $publication = Publication::with('poll.options')
            ->where('publications.id', $this->publicationId)
            ->where('contents.status', 'visible')
            ->first();

        if (!$publication || !$publication->poll) {
            return;
        }

        $poll = $publication->poll;
        $selectedOption = $poll->options->firstWhere('id', $optionId);
        if (!$selectedOption) {
            return;
        }

        DB::transaction(function () use ($poll, $selectedOption): void {
            $existingVote = PollVote::where('poll_id', $poll->id)
                ->where('user_id', auth()->id())
                ->lockForUpdate()
                ->first();

            if ($existingVote && $existingVote->poll_option_id === $selectedOption->id) {
                return;
            }

            if ($existingVote) {
                PollOption::where('id', $existingVote->poll_option_id)
                    ->where('votes_count', '>', 0)
                    ->decrement('votes_count');

                $existingVote->update(['poll_option_id' => $selectedOption->id]);
            } else {
                PollVote::create([
                    'poll_id' => $poll->id,
                    'poll_option_id' => $selectedOption->id,
                    'user_id' => auth()->id(),
                ]);
            }

            PollOption::where('id', $selectedOption->id)->increment('votes_count');
        });
    }

    public function addComment(): void
    {
        $this->validate([
            'newComment' => 'required|string',
        ]);

        $content = Content::create([
            'type' => 'comment',
            'status' => 'visible',
            'author_id' => auth()->id(),
        ]);

        DB::table('comments')->insert([
            'id' => $content->id,
            'text' => trim($this->newComment),
            'publication_id' => $this->publicationId,
            'parent_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->newComment = '';
    }

    public function addReply(string $parentId): void
    {
        $draft = $this->replyDrafts[$parentId] ?? '';
        if (!is_string($draft) || trim($draft) === '') {
            return;
        }

        $parentComment = Comment::query()
            ->where('comments.id', $parentId)
            ->where('comments.publication_id', $this->publicationId)
            ->first();

        if (!$parentComment) {
            return;
        }

        $content = Content::create([
            'type' => 'comment',
            'status' => 'visible',
            'author_id' => auth()->id(),
        ]);

        DB::table('comments')->insert([
            'id' => $content->id,
            'text' => trim($draft),
            'publication_id' => $this->publicationId,
            'parent_id' => $parentComment->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->replyDrafts[$parentId] = '';
        $this->openReplyForms[$parentId] = false;
    }

    public function toggleReplyForm(string $commentId): void
    {
        $this->openReplyForms[$commentId] = !($this->openReplyForms[$commentId] ?? false);
    }

    public function promptDeleteComment(string $commentId): void
    {
        $this->pendingDeleteCommentId = $commentId;
    }

    public function cancelDeleteComment(): void
    {
        $this->pendingDeleteCommentId = null;
    }

    public function confirmDeleteComment(): void
    {
        if (!$this->pendingDeleteCommentId) {
            return;
        }

        $commentId = $this->pendingDeleteCommentId;
        $comment = Comment::query()
            ->where('comments.id', $commentId)
            ->where('comments.publication_id', $this->publicationId)
            ->first();

        if (!$comment) {
            $this->pendingDeleteCommentId = null;
            return;
        }

        if (auth()->id() !== $comment->author_id && !auth()->user()->isAdmin()) {
            abort(403);
        }

        Content::where('id', $comment->id)->update(['status' => 'deleted']);
        $this->pendingDeleteCommentId = null;
    }

    public function render()
    {
        return view('livewire.post-page', [
            'publication' => $this->publication,
        ]);
    }
}
