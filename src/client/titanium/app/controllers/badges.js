var apiMapper = new ApiMapper();


// Badgeを取得
apiMapper.badge(
    {
        user_id: Alloy.Globals.user.user_id 
    },
    function(e){
        var response = JSON.parse(this.responseText);

        var items = new Array();
        var badge_count = response.length || 0;

        Ti.API.info(badge_count);
        if(badge_count == 0){
            Ti.API.info('No badges');
            $.message.text = "取得したバッジはありません";
        }

        for(var i=0; i<badge_count; i++){
            // alert(response[i].Badge.image_url);
            response[i].Badge.image_url = Alloy.Globals.app.img_endpoint + response[i].Badge.image_url;
            var window = Alloy.createController('_badge', response[i].Badge);
            items.push(window.getDashboardItem());
        }
        $.dashboard.setData(items);
    },
    function(e){
        alert(this.responseText);
    }
);
