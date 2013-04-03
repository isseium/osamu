var Alloy = require("alloy"), _ = Alloy._, Backbone = Alloy.Backbone;

Alloy.Globals.app = new Object;

Alloy.Globals.app.api_endpoint = "http://osamu-test.ap01.aws.af.cm/rsk/zishin/";

Alloy.createController("index");