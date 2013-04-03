var ApiMapper = require("apiMapper").ApiMapper;
var apiMapper = new ApiMapper();

// Badgeを取得
apiMapper.badge(
    function(e){
        var response = JSON.parse(this.responseText);


        var items = new Array();
        for(var i=0; i<response.length; i++){
            // alert(response[i].Badge.image_url);
            var window = Alloy.createController('_badge', response[i].Badge);
            items.push(window.getDashboardItem());
        }
        $.dashboard.setData(items);
    },
    function(e){
        alert(this.responseText);
    }
);
