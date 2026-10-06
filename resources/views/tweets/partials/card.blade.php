<div class="content_post">
  @if ($tweet->safe_image_url)
    <img class="content_post__image" src="{{ $tweet->safe_image_url }}" alt="" loading="lazy" referrerpolicy="no-referrer">
  @endif
  @php($isOwner = auth()->check() && (int) auth()->id() === (int) $tweet->user_id)
  @if ($showDetails || ($showActions && $isOwner))
    <div class="more">
      <span><img src="{{ asset('images/arrow_top.png') }}" alt="Menu"></span>
      <ul class="more_list">
        @if ($showDetails)
          <li><a class="post" href="{{ route('tweets.show', $tweet) }}">詳細</a></li>
        @endif
        @if ($showActions && $isOwner)
          <li><a class="post" href="{{ route('tweets.edit', $tweet) }}">編集</a></li>
          <li>
            <form action="{{ route('tweets.destroy', $tweet) }}" method="POST">
              @csrf
              @method('DELETE')
              <button type="submit" class="post">削除</button>
            </form>
          </li>
        @endif
      </ul>
    </div>
  @endif
  <p>{{ $tweet->text }}</p>
  <a class="nickname" href="{{ route('users.show', $tweet->user) }}">
    <span class="name">投稿者{{ $tweet->user->nickname }}</span>
  </a>
</div>
