var apiMapper = new ApiMapper();

// user_id 作成関数
function generateUser(){
    var seed = parseInt((new Date)/1000) + ":" + Math.floor( Math.random() * 1000000 );
    var user_id = Ti.Utils.sha1(seed);
    Alloy.Globals.user.user_id = user_id;

    apiMapper.useradd({user_id: user_id}, function(){
        Titanium.App.Properties.setString('user_id', user_id);
        Ti.API.info('new user generated');
    }, function(){
    });
}

// 初回起動時はuser_idを作成
if(! Ti.App.Properties.hasProperty('user_id')){
    Ti.API.info('No token. Try to generate user_id');
    generateUser();
}else{
    // user_id を取得
    Alloy.Globals.user.user_id = Titanium.App.Properties.getString('user_id');
}

// navbar の設定
var button_badges = Ti.UI.createButton({title:'バッジ一覧'});
var button_start_quiz = Ti.UI.createButton({title:'復興クイズ'});

// 削除ボタン
var button_truncate = Titanium.UI.createButton({
    systemButton: Titanium.UI.iPhone.SystemButton.TRASH
});
button_truncate.addEventListener('click', function(){
    var alertDialog = Titanium.UI.createAlertDialog({
        title: 'リセット',
        message: '成績、バッジなどすべての情報をリセットしますか？',
        buttonNames: ['OK','Cancel'],
        cancel: 1
    });
    alertDialog.addEventListener('click',function(event){
        // Cancelボタンが押されたかどうか
        if(event.cancel){
            // cancel時の処理: なにもしない
        }
        // 選択されたボタンのindexも返る
        if(event.index == 0){
            generateUser();
        }
    });
    alertDialog.show();
});


button_badges.addEventListener('click', function(){
    var window_badges = Alloy.createController('badges', {}).getView();
    window_badges.title = "取得したバッジ";
    window_badges.rightNavButton = button_truncate;
    $.nav.open(window_badges);
});

button_start_quiz.addEventListener('click', function(){
    var controller_quiz = Alloy.createController('quiz', {});
    var window_quiz = controller_quiz.getView();
    window_quiz.title = "復興クイズ";
    controller_quiz.setNavigation($.nav, window_quiz);
    $.nav.open(window_quiz);
});

$.win.leftNavButton = button_badges;
$.win.rightNavButton = button_start_quiz;

// 地図情報取得
$.win.addEventListener('focus', function(){
    apiMapper.establish(
        {
            user_id: Alloy.Globals.user.user_id 
        },
        function(e){
            var response = JSON.parse(this.responseText);

            var param_styles = new Array();
            for( var i=0; i<response.length; i++){
                param_styles.push("bm.p." + response[i].Establish.city_id + ":" + Alloy.Globals.app.ymap.paint_color);
            }

            var style = "style=bm";
            if(param_styles.length > 0){
                style = "style=" + param_styles.join("|");
            }

            // 地図画像取得 TODO: あとで外部設定ファイル化
            var image_url = "http://map.olp.yahooapis.jp/OpenLocalPlatform/V1/static?"
                    + "appid=" + Alloy.Globals.app.yahoo_appid
                    + "&lat=" + Alloy.Globals.app.ymap.lat
                    + "&lon=" + Alloy.Globals.app.ymap.lon
                    + "&z=" + Alloy.Globals.app.ymap.zoom
                    + "&height=" + Alloy.Globals.app.ymap.height
                    + "&width=" + Alloy.Globals.app.ymap.width
                    + "&mode=" + Alloy.Globals.app.ymap.mode
                    + "&" + style;
            Ti.API.debug(image_url);
            $.establish_map.image = image_url;
            $.establish_map.url = image_url;
        },
        function(e){
            alert('制覇情報の取得に失敗しました');
        }
    );
});

// View 表示
$.index.open();
