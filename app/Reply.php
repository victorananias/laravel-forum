<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Carbon\Carbon;

class Reply extends Model
{
    use Favoritable, RecordsActivity;

    protected $with = ['owner', 'favorites'];
    protected $appends = ['favoritesCount', 'isFavorited'];
    protected $fillable = ['thread_id', 'user_id', 'body'];

    public static function boot()
    {
        parent::boot();

        static::created(function ($reply) {
            $reply->thread->increment('replies_count');
        });

        static::deleting(function ($reply) {
            $reply->thread->decrement('replies_count');
        });
    }

    /**
     * A reply belongs to an owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * A reply belongs to an thread.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class, 'thread_id');
    }

    public function setBodyAttribute($body) {
        $this->attributes['body'] = preg_replace('/@([\w\-\_]+)/', '<a href="/profiles/$1">$0</a>', $body);
    }

    /**
     * Determinate the path to the reply.
     *
     * @return string
     */
    public function path()
    {
        return $this->thread->path() . "#reply-{$this->id}";
    }

    /**
     * Determinate if the reply was just published a moment ago.
     *
     * @return mixed
     */
    public function wasJustPublished()
    {
        return $this->created_at->gt(Carbon::now()->subMinute());
    }

    /**
     * Returns the name of the mentioned users.
     *
     * @return mixed
     */
    public function mentionedUsers()
    {
        preg_match_all('/@([\w\-\_]+)/', $this->body, $matches);

        return $matches[1];
    }
}
