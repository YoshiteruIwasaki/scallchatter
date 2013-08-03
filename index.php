<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>Android YUI</title>
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0-rc1/css/bootstrap.min.css">
</head>
<body>
<div class="row">
  <div class="col-lg-6 col-offset-3">
	<div class="jumbotron"><h1>私はAndroid YUI。</h1><p>壊れかけのAndroid。</p></div>
	<div id="twitter"></div>
	<div id="audio"></div>
  </div>
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0-rc1/js/bootstrap.min.js"></script>
<script>
$(function(){


	var $twtr = $('#twitter');

	// Ajaxステータスの表示
	$twtr.empty()
		.append('<p class="msg">ツイートの取得中…</p>');

	$.getJSON( './twitter?callback=?' )

		// 読み込み完了時
		.success(function( json ) {

			var tweetStr = ''; // ツイートHTML格納用
			var time, icon, name, text;

			// divを空に
			$twtr.empty();

			// JSONデータの処理
			$.each( json, function(i, tweet) {

				// JSONからデータを抽出
				name = tweet.user.screen_name;       // ユーザー名
				time = tweet.created_at;             // 日付
				icon = tweet.user.profile_image_url; // プロフィールアイコンURL
				text = tweet.text;                   // ツイート本文
				tweetVoice(text);

				// 日付のフォーマット
				time = formatDate(time);

				// プロフィールアイコン（最少サイズを使用）
				icon = icon.replace(/_normal/, '_mini');

				// ツイート内のリンク／ユーザー名／ハッシュタグ
				text = text.replace(/(s?https?:\/\/[-_.!~*'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)/gi, '<a href="$1" class="link">$1</a>');
				text = text.replace(/#(\w+)/gi,'<a href="http://twitter.com/#!/search?q=%23$1" class="hashtag">#$1</a>' );
				text = text.replace(/@(\w+)/gi,'<a href="http://twitter.com/#!/$1" class="user">@$1</a>' );

				// HTMLの整形
				tweetStr += '<div>';
				tweetStr += '<a href="http://twitter.com/#!/' + name + '"><img src="' + icon + '" alt="' + name + '" class="img-rounded" /></a></span>';
				tweetStr += '<span class="badge">' + name + '</span>';
				tweetStr += '<span class="label label-info"><small>' + time + '</small></span>';
				tweetStr += '<p>' + text + '</p>';
				tweetStr += '</div>';

			});

			// HTMLを追加
			$('<div></div>').html(tweetStr).appendTo($twtr);
		})

		// 読み込みエラー時
		.error(function( json ) {
			$twtr.empty()
				.append('<p class="error">エラー：ツイートを取得できませんでした。</p>');
		});


	var audioTagSupport = !!(document.createElement('audio').canPlayType);
	var $audio = $('#audio');

	function playAudio(value, count){
        try {
		var audio = document.createElement('audio');

		  audio.addEventListener( "error", function(){
			  console.log( "エラーが発生しました：" + audio.error );
			  count++;
			  if(count < 9){
			  sleep(1000);
			  playAudio(value, count);
			  }
		  }, false);

		  var text = "";
		  if(count == 0){
			  text = encodeURIComponent(value);
		  }else if(count == 1){
			  text = encodeURIComponent(" " + value);
		  }else if(count == 2){
			  text = encodeURIComponent(value) + ".";
		  }else if(count == 3){
			  text = encodeURIComponent(value) + "??";
		  }else if(count == 4){
			  text = encodeURIComponent(value) + ",";
		  }else if(count == 5){
			  text = encodeURIComponent(value) + "..";
		  }else if(count == 6){
			  text = encodeURIComponent("  " + value);
		  }else if(count == 7){
			  text = encodeURIComponent(value + "。");
		  }else if(count == 8){
			  text = encodeURIComponent(value) + "+";
		  }

		 if(value.match(/[^a-zA-Z]/)){
			audio.setAttribute('src', 'http://translate.google.com/translate_tts?tl=ja&q=' + text);
		 }else{
			audio.setAttribute('src', 'http://translate.google.com/translate_tts?tl=en&q=' + text);
		 }
		 audio.load();
	 	console.log(text);
		sleep(100);
		audio.play();
		console.log(audio);

        }catch (e) {
             // Fail silently but show in F12 developer tools console
              if(window.console && console.error("Error:" + e));
        }
	}

	function tweetVoice(text){
		if (!audioTagSupport) return false;
		if (text == '') return false;
		$.ajax({
			  type: "POST",
			  url: "./parse",
			  data: { value: text }
			}).done(function(response) {
				var string = $.trim(response);
			//	console.log(string);
				var arrayList = string.split(",");
				$.each(arrayList, function(key, value) {
					value = $.trim(value);
					value = value.replace(/\s+/g, "");
					var length = value.length;
				//	console.log(value);
					//$(this).delay(20000).queue(function() {
					if(length > 0){
						playAudio(value, 0);
						sleep(2000);
					}
					//   $(this).dequeue();
					// });
				});
				return false;
			});
	}
	function sleep(time) {
		  var d1 = new Date().getTime();
		  var d2 = new Date().getTime();
		  while (d2 < d1 + time) {
		    d2 = new Date().getTime();
		   }
		   return;
		}

	// 日付フォーマット用関数
	function formatDate( date ) {

		var dArr, dStr, d;

		// IEでパースできないフォーマットのため、
		// 文字列の順序を入れ替える
		dArr = date.split(" ");
		dStr = [dArr[0], dArr[1], dArr[2], dArr[5], dArr[3], dArr[4]].join(' ');
		d = new Date(dStr);

		return d.getFullYear() + '/' + (d.getMonth()+1) + '/' + d.getDate();

	}
	/**
	 右埋めする処理
	 指定桁数になるまで対象文字列の右側に
	 指定された文字を埋めます。
	 @param val 右埋め対象文字列
	 @param char 埋める文字
	 @param n 指定桁数
	 @return 右埋めした文字列
	**/
	function paddingright(val,char,n){
	 for(; val.length < n; val+=char);
	 return val;
	}

});
</script>
</body>
</html>
