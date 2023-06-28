{{-- ユーザが選択した楽曲をプレイリストに追加する際の最終画面 --}}
{{-- 追加ボタン、楽曲へ戻るボタンが押されたら「みんなのプレイリスト画面」に遷移 --}}

@php //前準備で変数を用意
$trackImage = $track->album->images[0]->url; //アルバム画像
$releaseDate = $track->album->release_date; //リリース日
    $dateFormat = date("Y年m月d日", strtotime($releaseDate));
$trackName = $track->name; //曲名
$artistName = $track->artists[0]->name; //アーティスト名
$trackTime = $track->duration_ms; //曲の再生時間
    $seconds = floor($trackTime/1000);
    $minutes = floor($seconds/60);
    $seconds = $seconds%60;
    $secondsFormat = sprintf('%02d', $seconds);
$albumName = $track->album->name; //アルバム名
$trackPreview = $track->preview_url; //プレビューのURL(日本の曲は対応してないことが多い)

//アーティスト情報
$artistImage = $artist->images[0]->url; //アーティストの宣材写真
@endphp

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <title>プレイリストへ追加</title>
</head>

<body>
    <header class="border-bottom border-1 border-secondary">
        <nav class="navbar navbar-light bg-light">
            <h1>プレイリストに追加</h1>
                <p class="navbar-text mt-3">
                    {{ Auth::user() -> name }} さん ログイン中
                </p>
            <ul class="nav justify-content-end">
                <li class="nav-item">
                    <form action="index" method="get">
                        <button class="btn btn-primary mr-3" type="submit">ホーム</button>
                    </form>
                </li>
                <li class="nav-item">
                    <form action="everyone_playlist" method="get">
                        <button class="btn btn-primary mr-3" type="submit">みんなのプレイリスト</button>
                    </form>
                </li>
                <li class="nav-item">
                    <form action="add_region" method="get">
                        <button class="btn btn-primary mr-3" type="submit">登録地追加</button>
                    </form>
                </li>
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button class="btn btn-danger" type="submit">ログアウト</button>
                    </form>
                </li>
            </ul>
        </nav>
    </header>

    {{-- ここから本文 --}}
    <div class="container m-10 p-10 rounded bg-dark text-white">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-md-auto text-center">
                    <div class="text-center"><br>
                        <img src={{$trackImage}} width=350><br>
                        <span style="font-size: 40px;" class="fw-bold">{{$trackName}}</span>　　{{$minutes}}:{{$secondsFormat}}<br>
                        <span style="font-size: 20px;" class="fw-bold">{{$albumName}}</span><br>
                        <div class="container-fluid">
                            <div class="row justify-content-center align-items-center">
                                <div class="col-md-auto text-center">
                                    <img src={{$artistImage}} width=100 class="rounded-circle">
                                </div>
                                <div class="fs-1 text-left align-middle">{{$artistName}}<br>
                                    @foreach($artist->genres as $genre)
                                        {{$genre}}　
                                    @endforeach<br>
                                    {{$dateFormat}}
                                </div>
                            </div>
                        </div><br>
                        @if ($trackPreview)
                            <audio controls class="w-400">
                                <source src="{{$trackPreview}}" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio><br>
                        @else
                            Preview not available.<br>
                        @endif
                    </div><br>
                </div>
                {{-- ここまでが左側の要素 --}}
                <div class="col-md-auto">
                    <div class="text-right m-1">
                        <form action="" method="POST">
                            @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif
                            <span class="m-2">プレイリスト名 </span>
                            <input type="text" name="playlist_name" id="playlist_name" placeholder="新規プレイリスト"><br><br>
                            <span class="m-2">既存プレイリスト </span>
                            <select name="list_id" id="list_id" class="form-select form-select-lg">
                                <option value="">-</option>
                                @foreach ($playlists as $playlist)
                                    <option value="{{$playlist->id}}">{{$playlist->list_name}}</option>
                                @endforeach
                            </select><br><br>
                            <span class="m-2">曲名 </span>
                            <input type="text" name="title" value="{{$track->name}}" readonly><br><br>
                            <span class="m-2">アーティスト </span>
                            <input type="text" name="artist" value="{{$track->artists[0]->name}}" readonly><br><br>
                            <input type="hidden" name="trackId" value="{{$trackId}}">
                            <input type="submit" value="追加" class="btn btn-success btn-block btn-lg" id="add_button" disabled><br>
                            @csrf
                        </form>
                    </div>
                    <div class="text-right">
                        <button class="btn btn-info btn-block btn-lg" onclick="goBack()">
                            <b>戻る</b>
                        </button>

                        <script>
                        function goBack() {
                        window.history.back();
                        }
                        </script>
                    </div>
                </div>

                <script>
                    // 入力状態を監視し、どちらか一方が入力されている場合に追加ボタンを有効化する
                    const playlistNameInput = document.getElementById('playlist_name');
                    const listIdSelect = document.getElementById('list_id');
                    const addButton = document.getElementById('add_button');

                    playlistNameInput.addEventListener('input', toggleAddButton);
                    listIdSelect.addEventListener('change', toggleAddButton);

                    function toggleAddButton() {
                        const playlistNameValue = playlistNameInput.value.trim();
                        const listIdValue = listIdSelect.value;

                        if ((playlistNameValue !== '' && listIdValue === '') || (playlistNameValue === '' && listIdValue !== '')) {
                            addButton.disabled = false;
                        } else {
                            addButton.disabled = true;
                        }
                    }
                </script>


            </div>
        </div>
    </div>
</body>
</html>
