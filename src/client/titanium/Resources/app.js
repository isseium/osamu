var Alloy = require("alloy"), _ = Alloy._, Backbone = Alloy.Backbone;

Ti.include("app_config.js");

Alloy.Globals.user = new Object;

Alloy.Globals.app.ymap = {
    lat: 38.2,
    lon: 140.6,
    zoom: 9,
    height: 1600,
    width: 600,
    mode: "blankmap",
    paint_color: "00CC66"
};

var ApiMapper = require("apiMapper").ApiMapper;

Alloy.createController("index");