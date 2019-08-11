@section('tweet')
      <div class="content_post" style="background-image: url( '{{$tweet->image}}' );">
        <div class="more">
          <span><img src="{{asset('images/arrow_top.png')}}" alt="Logo"></span>
          <ul class="more_list">
            <li>
              <a class="post" href="/tweets/{{$tweet->id}}">詳細</a>
            </li>
            @if(Auth::check() && Auth::id() === $tweet->user->id)
            <li>
              <a class="post" href="/tweets/{{$tweet->id}}/edit">編集</a>
            </li>
            <li>
              <form id="delete" action="{{ route('tweets.destroy',$tweet->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <a class="post" href="javascript:void(0)" onclick="this.parentNode.submit()">削除</a>
              @endif
              </form>
            </li>
          </ul>
        </div>
        <p>{{ $tweet->text }}</p>
          <a class="nickname" href="/users/{{$tweet->user->id}}">
        <span class="name">投稿者{{ $tweet->user->nickname }}</span>
        </a>
      </div>
@endsection
