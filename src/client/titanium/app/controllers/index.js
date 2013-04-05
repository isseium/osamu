var apiMapper = new ApiMapper();

// navbar の設定
var button_badges = Ti.UI.createButton({title:'Badges'});
var button_start_quiz = Ti.UI.createButton({title:'Quiz'});

button_badges.addEventListener('click', function(){
    var window_badges = Alloy.createController('badges', {}).getView();
    $.nav.open(window_badges);
});

button_start_quiz.addEventListener('click', function(){
    var window_badges = Alloy.createController('quiz', {}).getView();
    $.nav.open(window_badges);
});

$.win.leftNavButton = button_badges;
$.win.rightNavButton = button_start_quiz;

// 地図情報取得
apiMapper.establish(
    {
        user_id: Alloy.Globals.user.user_id 
    },
    function(e){
        var response = JSON.parse(this.responseText);

        var param_styles = new Array();
        for( var i=0; i<response.length; i++){
            param_styles.push("bm.p." + response[i].Establish.city_id + ":cc0000");
        }

        // 地図画像取得
        var image_url = "http://map.olp.yahooapis.jp/OpenLocalPlatform/V1/static?appid=buxd.Puxg662idT4Kd8LGdoNWBKywhPDbp4dfPupYfrbu.0ICiES_bkM9iImHA--&lat=38.2&lon=140.6&z=9&height=1600&width=600&mode=blankmap&style=" + param_styles.join("|");
        Ti.API.debug(image_url);
        $.establish_map.image = image_url;
        $.establish_map.url = image_url;
    },
    function(e){
        alert('制覇情報の取得に失敗しました');
    }
);

// View 表示
$.index.open();
