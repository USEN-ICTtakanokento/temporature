@php
use App\Models\Region_name;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

$fir_weathers = $data[0]["weathers"];

if (Str::contains($fir_weathers[0], '雨')) {
    $message = "傘は持ちましたか？";
} elseif(Str::contains($fir_weathers[0], '晴')){
    $message = "お出かけ日和ですね！";
} else {
    $message = "お疲れ様です！今日もかっこよく働きましょう！";
}

@endphp


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- BootStrap Link --}}
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">

    {{-- フォント Link --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lobster+Two&display=swap" rel="stylesheet">


    {{-- 自作CSSファイル --}}
    @vite(['resources/css/index_css.css'])
    <title>index</title>
</head>

<body>
    <header class="border-bottom border-1 border-secondary">
        <nav class="navbar navbar-light bg-light">
            {{-- <img src="\images\kawaii\sunny.png" alt="ロゴ" width="60px"> --}}
            <h1 class="Lobster">Temporature</h1>
                <p class="navbar-text mt-3">
                    {{ Auth::user() -> name }} さん {{$message}}
                </p>
            <ul class="nav justify-content-end">
                <li class="nav-item">
                    <form action="myplaylist" method="get">
                        <button class="btn btn-primary mr-3" type="submit">マイプレイリスト</button>
                    </form>
                </li>
                <li class="nav-item">
                    <form action="everyone_playlist" method="get">
                        <button class="btn btn-primary mr-3" type="submit">楽曲一覧</button>
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

    <main class="content-container">
        <div style="display: flex; justify-content: space-between;">
            <div class="weather_table" style="margin-right: 10px;">
                <table>
                    <div class="container">
                        <table class="table table-bordered">
                        <thead>
                        <tr class="bg-dark text-white">
                            <th><div class="column_headers">地域名</div></th>
                            <th><div class="column_headers">今日の天気</div></th>
                            <th><div class="column_headers">明日の天気</div></th>
                            <th><div class="column_headers">明後日の天気</div></th>
                            <th><div class="column_headers">削除</div></th>
                        </tr>
                    </thead>
                    <tbody>
                    @php
                        #表示するのは、都道府県名（DB）・地域名(API)・気象情報(API)
                        foreach ($data as $table_data){
                        $area = $table_data["area"];
                        $prefecture = $table_data["prefecture"];
                        $id = $table_data["id"];
                        $weathers = $table_data["weathers"];

                        $replace_area = array(
                                        "地方" => "地域",
                                        );
                            $area = str_replace(array_keys($replace_area), array_values($replace_area), $area);
                            echo <<<_TABLE_
                                <tr>
                                    <td>
                                        <div class="align-middle text-center areas_name">{$prefecture}<br>{$area}</div>
                                    </td>
_TABLE_;
                            for ($i=0; $i < 3; $i++) {
                                $result = Controller::weatherToIcon($weathers[$i]);
                                echo "<td class='align-middle text-center weather_forecasts'>". $result. "<br>　</td>";
                            }
                            echo "<td class='align-middle text-center delete'><form action='/delete/{$id}'><button type='submit' class='btn btn-danger'>削除</botton></form></td></tr>";
                        }
                    @endphp
                </table>
            </div>
            <div style="flex: 1; margin-left: 10px;">
                <table class="table table-bordered col-12">
                    <tr class="bg-dark text-white">
                        <th><div class="column_headers">ジャケット</div></th>
                        <th><div class="column_headers">曲名</div></th>
                        <th><div class="column_headers">アーティスト</div></th>
                    </tr>
                    @foreach ($data as $fav_region)
                        <?php $results = Controller::weatherTracks($fav_region, $api); ?>
                        @foreach ($results->tracks->items as $counter => $song)
                            @if ($counter > 2)
                                @break
                            @endif
                            <tr>
                                <td class="aimage">
                                    <div class="album_image">
                                        <a href="/information?information={{ $song->id }}"><img src='{{ $song->album->images[0]->url }}' alt="Album Image" height="75.6px"></a>
                                    </div>
                                </td>
                                <td class="align-middle text-center col-6 aid">
                                    <b><a href="/information?information={{ $song->id }}">{{ $song->name }}</a></b>
                                </td>
                                <td class="align-middle text-center overflow-hidden artist_name">
                                    <b>{{ $song->artists[0]->name }}</b>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </table>
            <div>
        </div>
    </main>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>



